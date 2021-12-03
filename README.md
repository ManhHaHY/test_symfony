This task evaluates the candidate's skills in `PHP 7` and `Symfony 3.4`.

# PHP 7 - Symfony - REST Task API

## Introduction

You are working on a REST API that allows its users to read, create, update and delete tasks. The API is secured using the `apikey` header and is using JSON to communicate with users. The API itself is implemented using `FOSRestBundle` and the main controller responsible for application logic is called `TasksController`.

## Task definition

Your goal is to finish the implementation of the API and make it robust and secure. All places that require some implementation are tagged with `@TODO` annotation (including config files).

To complete this task you should:

* Configure security:
  * to access control entry `- { path: ^/api/tasks, roles: ROLE_WRITE }` add requirement to make it work only for methods POST, PUT and DELETE
  * define role hierarchy to be `ROLE_WRITE: ROLE_READ`

* Return JSON response for unauthorised requests in `ApiKeyAuthenticator::onAuthenticationFailure(Request $request, AuthenticationException $exception)` with `HTTP_FORBIDDEN` status code

* Configure service container:
  * `api_key_store` service in the `services.xml` requires passing two arrays with read and write keys (more information in the configuration file).
  * `api_key_user_provider` service requires passing `api_key_store` as its constructor argument

* Implement method `public function apply(Request $request, ParamConverter $configuration)` of `TaskParamConverter`

* Configure cache for GET actions:
  * Configure Cache annotation that will add cache control header and privately cache `getTaskAction(Task $task)` response for `60` seconds
  * Configure Cache annotation that will add cache control header and privately cache `getTasksAction()` response for `30` seconds

* Return errors and a valid status code in `TasksController::processTask(Task $task, Request $request, $verb, $successResponseCode)`
