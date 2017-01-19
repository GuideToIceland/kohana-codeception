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
	 */
	protected function doRequest($request)
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
		$content = $kohanaRequest->execute()->render();

		$headers = (array) $kohanaRequest->headers();
		$headers['Content-Type'] = "text/html; charset=UTF-8";
		$response = new Response($content, 200, $headers);
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
