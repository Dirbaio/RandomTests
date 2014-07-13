<?php

class TemplateLoader implements Twig_LoaderInterface
{
	public function getSource($name)
	{
		return file_get_contents($this->findTemplate($name));
	}
	public function getCacheKey($name)
	{
		return $this->findTemplate($name);
	}
	public function isFresh($name, $time)
	{
		return filemtime($this->findTemplate($name)) <= $time;
	}

	protected function findTemplate($name)
	{
		$path = ModuleHandler::getFile('/templates/'.$name);
		if(!$path)
			throw new Twig_Error_Loader(sprintf('Unable to find template "%s".', $name));
		return $path;
	}
}

class Template
{
	public static function render($file, $vars)
	{
		$loader = new TemplateLoader();
		$twig = new Twig_Environment($loader, array(
		    'cache' => '/tmp/twig_cache',
		    'auto_reload' => true,
		));
		
		$template = $twig->loadTemplate($file);
		echo $template->render($vars);
	}
}