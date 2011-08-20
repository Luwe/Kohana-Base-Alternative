<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Placeholder for global methods and attributes used in all View objects
 * 
 * Can only be extended
 * 
 * To ensure interchangeability of a view object with different formats (XML, JSON, HTML) raw HTML should
 * not be planted in View objects. HTML and Form helper functions should therefore not be used
 * when different formats are desired.
 * 
 * @package    LJCore
 * @author     Lieuwe Jan Eilander
 * @copyright  (c) 2010-2011 Lieuwe Jan Eilander
 */
abstract class Ljcore_View_Core extends Kostache_Layout {}
