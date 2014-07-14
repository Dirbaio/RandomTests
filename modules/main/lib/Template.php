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
	private static $twig = null;

	private static function load()
	{
		$loader = new TemplateLoader();
		self::$twig = new Twig_Environment($loader, array(
		    'cache' => '/tmp/twig_cache',
		    'auto_reload' => true,
		));

		self::$twig->addFilter(new Twig_SimpleFilter('slugify', function ($string) {
		    return Url::slugify($string);
		}));

	}

	public static function render($file, $vars)
	{
		if(!self::$twig)
			self::load();
		
		$template = self::$twig->loadTemplate($file);
		echo $template->render($vars);
	}
}