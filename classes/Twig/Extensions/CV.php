<?php
namespace Twig\Extensions;

class CV extends \Twig_Extension
{
    public function getName()
    {
        return 'cv';
    }

    public function getFilters()
    {
    	return array(
    		'time' => new \Twig_Filter_Method($this, 'time', array('needs_environment' => true)),
    	);
    }

    public function time(\Twig_Environment $env, $date, $format = '%c')
    {
    	$date = twig_date_converter($env, $date);
    	return strftime($format, $date->getTimestamp());
    }
}
