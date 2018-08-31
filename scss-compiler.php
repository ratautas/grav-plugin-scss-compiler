<?php
namespace Grav\Plugin;

use Grav\Common\Plugin;
use RocketTheme\Toolbox\Event\Event;

use Leafo\ScssPhp\Compiler;
use Leafo\ScssPhp\Server;

/**
 * Class ScssCompilerPlugin
 * @package Grav\Plugin
 */
class ScssCompilerPlugin extends Plugin
{
    /**
     * @return array
     *
     * The getSubscribedEvents() gives the core a list of events
     *     that the plugin wants to listen to. The key of each
     *     array section is the event that the plugin listens to
     *     and the value (in the form of an array) contains the
     *     callable (or function) as well as the priority. The
     *     higher the number the higher the priority.
     */
    public static function getSubscribedEvents()
    {
        return [
            'onPluginsInitialized' => ['onPluginsInitialized', 0]
        ];
    }

    /**
     * Initialize the plugin
     */
    public function onPluginsInitialized()
    {
        // Don't proceed if we are in the admin plugin
        if ($this->isAdmin()) return;

        // Enable the main event we are interested in
        $this->enable(['onAssetsInitialized'   => ['onAssetsInitialized', 0]]);
    }


    public function onAssetsInitialized()
    {
        if (!$this->isAdmin()) {
            dump($this);

            require_once 'vendor/scssphp/scss.inc.php';
            $theme_name = $this->grav['theme']['name'];
            $theme_path = './user/themes/'.$theme_name.'/';
            $css_dir = $this->grav['config']->get('plugins.scss-compiler.css_dir');
            $css_file = $this->grav['config']->get('plugins.scss-compiler.css_file');
            $scss_dir = $this->grav['config']->get('plugins.scss-compiler.scss_dir');
            $scss_file = $this->grav['config']->get('plugins.scss-compiler.scss_file');
            $css_dir_path = $theme_path.$css_dir;
            $css_file_path = $css_dir_path.$css_file;
            $scss_dir_path = $theme_path.$scss_dir;
            $scss_file_path = $scss_dir_path.$scss_file;
            if (!file_exists($css_dir_path)) mkdir($css_dir_path, 0755);
            if (file_exists($scss_file_path)) {
                $scss_content = file_get_contents($scss_file_path);
                $scss = new Compiler();
                $scss->setImportPaths($scss_dir_path);
                $scss->setFormatter('Leafo\ScssPhp\Formatter\Compressed');
                $css_content = $scss->compile($scss_content);
                file_put_contents($css_file_path, $css_content);
                $this->grav['assets']->addCss($css_file_path);
            }
        }
    }
}