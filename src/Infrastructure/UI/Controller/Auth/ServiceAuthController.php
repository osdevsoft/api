<?php

namespace Osds\Api\Infrastructure\UI\Controller\Auth;

use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;

use Osds\Api\Infrastructure\UI\Controller\BaseUIController;

use Osds\Api\Domain\Bus\Query\QueryBus;
use Osds\DDDCommon\Infrastructure\Log\LoggerInterface;
use Osds\Auth\Infrastructure\UI\ServiceAuth;

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
    private $serviceAuth;

    public function __construct(
        QueryBus $queryBus,
        LoggerInterface $logger,
        ServiceAuth $serviceAuth
    ) {
        $this->queryBus = $queryBus;
        $this->logger = $logger;
        $this->serviceAuth = $serviceAuth;
    }

    /**
     *
     * @Route(
     *     "",
     *     methods={"POST"}
     * )
     *
     * Tries to authenticate an user
     *
     * @SWG\Parameter(
     *     name="email",
     *     in="formData",
     *     type="string",
     *     description="User to authenticate"
     * )
     * @SWG\Parameter(
     *     name="password",
     *     in="formData",
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
     * @param $email
     * @param $password
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */

    public function handle()
    {

        $result = '';

        try {
            $requestParameters = $this->build()['post'];
            $messageObject = $this->getQueryMessageObject($requestParameters);

            $result = $this->queryBus->ask($messageObject);
            if ($result['total_items'] != 1) {
                throw new ItemNotFoundException;
            }
            $user = $result['items'][0];
            if (!password_verify($requestParameters['password'], $user['password'])) {
                throw new UnauthorizedException;
            }

            unset($user['password']);
            $authToken = $this->serviceAuth->encodeServiceToken($user);

            $result = [
                'authToken' => $authToken,
                'User' => [
                    'name' => $user['username']
                ]
            ];
        } catch (UnauthorizedException $e) {
            $e->setLogger($this->logger);
            $e->setMessage($requestParameters['email'], $e);
            $result = $e->getResponse();
        } catch (ItemNotFoundException $e) {
            $e->setLogger($this->logger);
            $e->setMessage('Admin', $requestParameters['email']);
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
        if (!isset($requestParameters['email'])
            || !isset($requestParameters['password'])
        ) {
            throw new BadRequestException();
        }

        return new ServiceAuthQuery(
            'User',
            $requestParameters['email']
        );
    }
}
