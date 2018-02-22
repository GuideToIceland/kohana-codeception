<?php
namespace Codeception\Lib\Connector;

use Symfony\Component\BrowserKit\Request;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\BrowserKit\Client;

class KohanaConnector extends Client {

	public function setIndex($index)
	{
		$this->index = $index;
	}

	/**
	 * Execute a request.
	 *
	 * @param SymfonyRequest $request
	 *
	 * @return Response
	 *
	 * @author Thoi
	 * @modified Andri Thorlacius <andri.thorlacius@gmail.com>
	 */
	protected function doRequest($request)
	{
		try
		{
			$_COOKIE = $request->getCookies();
			$_SERVER = $request->getServer();
			$_FILES  = $request->getFiles();

			$uri = ($_SERVER['HTTPS'] === TRUE) ? 'https://' : 'http://';
			$uri .= $_SERVER['HTTP_HOST'];
			$uri = str_replace($uri, '', $request->getUri());

			$_SERVER['KOHANA_ENV'] = 'TESTING';
			$_SERVER['REQUEST_METHOD'] = strtoupper($request->getMethod());
			$_SERVER['REQUEST_URI'] = strtoupper($uri);

			$this->_initRequest();

			$kohanaRequest = \Request::factory($uri);
			$kohanaRequest->method($_SERVER['REQUEST_METHOD']);

			if (strtoupper($request->getMethod()) == 'GET')
			{
				$kohanaRequest->query($request->getParameters());
			}
			if (strtoupper($request->getMethod()) == 'POST')
			{
				$kohanaRequest->post($request->getParameters());
			}

			$kohanaRequest->cookie($_COOKIE);

			$kohanaRequest::$initial = $kohanaRequest;

			$executedRequest = $kohanaRequest->execute();
			$content = $executedRequest->render();

			$response = new Response($content, $executedRequest->status(), (array) $executedRequest->headers());
		}
		catch (Exception $e)
		{
			$response = new Response($e->getMessage(), 500, []);
		}
		return $response;
	}

	protected function _initRequest()
	{
		static $is_first_call;
		if ($is_first_call === NULL)
		{
			$is_first_call = TRUE;
		}
		if ($is_first_call)
		{
			$is_first_call = NULL;

			include $this->index;
		}
	}
}
