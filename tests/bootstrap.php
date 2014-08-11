<?php
//
// Unit Test Bootstrap and Slim PHP Testing Framework
// =============================================================================
//
// SlimpPHP is a little hard to test - but with this harness we can load our
// routes into our own `$app` container for unit testing, and then `run()` and
// hand a reference to the `$app` to our tests so that they have access to the
// dependency injection container and such.
//
// * Author: [Craig Davis](craig@there4development.com)
// * Since: 10/2/2013
//
// -----------------------------------------------------------------------------

/**
 * Class Slim_Framework_TestCase
 *
 * @method get
 * @method post
 * @method delete
 */
class Slim_Framework_TestCase extends PHPUnit_Framework_TestCase
{
    // We support these methods for testing. These are available via
    // `this->get()` and `$this->post()`. This is accomplished with the
    // `__call()` magic method below.
    private $testingMethods = array('get', 'post', 'patch', 'put', 'delete', 'head');

    /**
     * @var \Slim\Slim
     */
    protected $app;

    /**
     * @var \Slim\Http\Request
     */
    protected $request;

    /**
     * @var \Slim\Http\Response
     */
    protected $response;

    /**
     * @var \shina\controlmybudget\User
     */
    protected $user;

    // Run for each unit test to setup our slim app environment
    public function setup()
    {
        // Initialize our own copy of the slim application
        $app = new \Slim\Slim(array(
            'version' => '0.0.0',
            'debug' => false,
            'mode' => 'testing'
        ));
        $app->container->set(
            'config',
            [
                'db' => array(
                    'driver' => 'pdo_sqlite',
                    'memory' => true
                )
            ]
        );
        require __DIR__ . '/../app/di-container.php';

        $cli = new \Symfony\Component\Console\Application();
        $cli->setHelperSet(
            new \Symfony\Component\Console\Helper\HelperSet(array(
                'db' => new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($app->conn),
                'dialog' => new \Symfony\Component\Console\Helper\DialogHelper()
            ))
        );
        $cli->add(new \Doctrine\DBAL\Migrations\Tools\Console\Command\MigrateCommand());
        $input = new \Symfony\Component\Console\Input\ArrayInput([
            'migrations:migrate'
        ]);
        $input->setInteractive(false);
        $cli->find('migrations:migrate')->run($input, new \Symfony\Component\Console\Output\NullOutput());

        $this->user = $app->user_service->getById(1);

        // Include our core application file
        require __DIR__ . '/../app/main.php';

        $mock_plugin = new \Guzzle\Plugin\Mock\MockPlugin();
        $mock_plugin->addResponse(
            new \Guzzle\Http\Message\Response(200, [], json_encode(
                    [
                        'id' => $this->user->facebook_user_id
                    ]
                )
            ));
        $app->http->addSubscriber($mock_plugin);

        // Establish a local reference to the Slim app object
        $this->app = $app;
    }

    // Abstract way to make a request to SlimPHP, this allows us to mock the
    // slim environment
    private function request($method, $path, $formVars = array(), $optionalHeaders = array())
    {
        // Capture STDOUT
        ob_start();

        $formVars = array_merge(['access_token' => '111'], $formVars);

        // Prepare a mock environment
        \Slim\Environment::mock(
            array_merge(
                array(
                    'REQUEST_METHOD' => strtoupper($method),
                    'PATH_INFO' => $path,
                    'SERVER_NAME' => 'local.dev',
                    'slim.input' => http_build_query($formVars),
                    'QUERY_STRING' => http_build_query($formVars)
                ),
                $optionalHeaders
            )
        );

        // Establish some useful references to the slim app properties
        $this->request = $this->app->request();
        $this->response = $this->app->response();

        // Execute our app
        $this->app->run();

        // Return the application output. Also available in `response->body()`
        return ob_get_clean();
    }

    // Implement our `get`, `post`, and other http operations
    public function __call($method, $arguments)
    {
        if (in_array($method, $this->testingMethods)) {
            list($path, $formVars, $headers) = array_pad($arguments, 3, array());
            return $this->request($method, $path, $formVars, $headers);
        }
        throw new \BadMethodCallException(strtoupper($method) . ' is not supported');
    }

}