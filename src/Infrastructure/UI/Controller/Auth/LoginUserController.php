<?php

namespace Osds\Api\Infrastructure\UI\Controller\Auth;

use Osds\Api\Infrastructure\UI\Controller\BaseUIController;

use Illuminate\Http\Request;
use Osds\Api\Domain\Bus\Query\QueryBus;
use Osds\Api\Infrastructure\Log\LoggerInterface;
use Osds\Api\Infrastructure\Auth\AuthInterface;
use Symfony\Component\Security\Core\User\UserInterface;

use Osds\Api\Application\Auth\LoginUserQuery;

use Osds\Api\Domain\Exception\BadRequestException;
use Osds\Api\Domain\Exception\UnauthorizedException;
use Osds\Api\Domain\Exception\ItemNotFoundException;
use Osds\Api\Domain\Exception\ErrorException;


use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;

/**
 * @Route("/api/auth/login")
 */
class LoginUserController extends BaseUIController
{

    protected $request;
    private $queryBus;
    private $logger;
    private $authenticator;
    private $authUser;

    public function __construct(
        Request $request,
        QueryBus $queryBus,
        LoggerInterface $logger,
        AuthInterface $authenticator,
        UserInterface $authUser
    ) {
        $this->request = $request;
        $this->queryBus = $queryBus;
        $this->logger = $logger;
        $this->authenticator = $authenticator->manager();
        $this->authUser = $authUser;
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
     */

    public function handle()
    {
        $result = '';

        try {
            $this->build($this->request);

            $messageObject = $this->getQueryMessageObject($this->request);

            $result = $this->queryBus->ask($messageObject);
            if ($result['total_items'] != 1) {
                throw new ItemNotFoundException;
            }
            $user = $result['items'][0];
            if (!password_verify($this->request->parameters['password'], $user['password'])) {
                throw new UnauthorizedException;
            }

            $this->authUser->setUsername($user['email']);
            $this->authUser->setPassword($user['password']);
            $authToken = $this->authenticator->create($this->authUser);

            $result = [
                'authToken' => $authToken,
                'user' => [
                    'name' => $user['name']
                ]
            ];
        } catch (UnauthorizedException $e) {
            $e->setLogger($this->logger);
            $e->setMessage($this->request->parameters['username'], $e);
            $result = $e->getResponse();
        } catch (ItemNotFoundException $e) {
            $e->setLogger($this->logger);
            $e->setMessage('user', $this->request->parameters['username']);
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

    private function getQueryMessageObject($request)
    {
        if (!isset($request->parameters['username'])
            || !isset($request->parameters['password'])
        ) {
            throw new BadRequestException();
        }

        return new LoginUserQuery(
            'user',
            $request->parameters['username']
        );
    }
}
