<?php

namespace Osds\Api\Infrastructure\UI\Controller\Auth;

use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;

use Osds\Api\Infrastructure\UI\Controller\BaseUIController;

use Osds\Api\Domain\Bus\Query\QueryBus;
use Osds\DDDCommon\Infrastructure\Log\LoggerInterface;
use Osds\Api\Infrastructure\Auth\AuthInterface;
use Symfony\Component\Security\Core\User\UserInterface;

use Osds\Api\Application\Auth\ServiceAuthQuery;

use Osds\Api\Domain\Exception\BadRequestException;
use Osds\Api\Domain\Exception\UnauthorizedException;
use Osds\Api\Domain\Exception\ItemNotFoundException;
use Osds\Api\Domain\Exception\ErrorException;

/**
 * @Route("/apiServiceAuth")
 */
class ServiceAuthController extends BaseUIController
{

    private $queryBus;
    private $logger;
    private $authenticator;
    private $authUser;

    public function __construct(
        QueryBus $queryBus,
        LoggerInterface $logger,
        AuthInterface $authenticator,
        UserInterface $authUser
    ) {
        $this->queryBus = $queryBus;
        $this->logger = $logger;
        $this->authenticator = $authenticator->manager();
        $this->authUser = $authUser;
    }

    /**
     *
     * @Route(
     *     "",
     *     methods={"GET"}
     * )
     *
     * Tries to authenticate an user
     *
     * @SWG\Parameter(
     *     name="username",
     *     in="query",
     *     type="string",
     *     description="User to authenticate"
     * )
     * @SWG\Parameter(
     *     name="password",
     *     in="query",
     *     type="string",
     *     description="Password of the user to authenticate"
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Returns a JWT or an error",
     *     )
     * )
     * @SWG\Tag(name="auth")
     * @Security(name="Bearer")
     *
     * @param $username
     * @param $password
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */

    public function handle()
    {

        $result = '';

        try {
            $requestParameters = $this->build();
            $messageObject = $this->getQueryMessageObject($requestParameters['get']);

            $result = $this->queryBus->ask($messageObject);
            if ($result['total_items'] != 1) {
                throw new ItemNotFoundException;
            }
            $user = $result['items'][0];
            if (!password_verify($requestParameters['get']['password'], $user['password'])) {
                throw new UnauthorizedException;
            }

            $this->authUser->setUsername($user['username']);
            $this->authUser->setPassword($user['password']);
            $authToken = $this->authenticator->create($this->authUser);

            $result = [
                'authToken' => $authToken,
                'User' => [
                    'name' => $user['username']
                ]
            ];
        } catch (UnauthorizedException $e) {
            $e->setLogger($this->logger);
            $e->setMessage($requestParameters['username'], $e);
            $result = $e->getResponse();
        } catch (ItemNotFoundException $e) {
            $e->setLogger($this->logger);
            $e->setMessage('Admin', $requestParameters['username']);
            $result = $e->getResponse();
        } catch (BadRequestException $e) {
            $e->setLogger($this->logger);
            $e->setMessage('Invalid parameters on the request', $e);
            $result = $e->getResponse();
        } catch (\Exception $e) {
            $exception = new ErrorException();
            $exception->setLogger($this->logger);
            $exception->setMessage('Server Error', $e);
            $result = $exception->getResponse();
        }


        return $this->generateResponse($result);
    }

    private function getQueryMessageObject($requestParameters)
    {
        if (!isset($requestParameters['username'])
            || !isset($requestParameters['password'])
        ) {
            throw new BadRequestException();
        }

        return new ServiceAuthQuery(
            'User',
            $requestParameters['username']
        );
    }
}
