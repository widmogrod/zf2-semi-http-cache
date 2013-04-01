<?php
namespace WidHttpCache;

class Config
{
    const CONFIG_NAMESPACE = 'zf2-semi-http-cache';
    const CONFIG_LISTENER = 'WidHttpCache\Listener\RoutesCacheConfigListener';

    protected $enabled = false;
    protected $useModifiedSince = true;
    protected $maxAge;
    protected $sMaxAge;
    protected $mustRevalidate = true;
    protected $configListener = self::CONFIG_LISTENER;

    public function __construct(array $options = null)
    {
        if (null !== $options) {
            $this->setOptions($options);
        }
    }

    public function setOptions(array $options)
    {
        foreach ($options as $key => $value) {
            switch ($key) {
                case 'enabled':
                    $this->setEnabled($value);
                    break;
                case 'useModifiedSince':
                    $this->setUseModifiedSince($value);
                    break;
                case 'configListener':
                    $this->setConfigListener($value);
                    break;
                case 'default':
                    $this->merge($value);
                    break;
            }
        }
    }

    public function setConfigListener($configListener)
    {
        $this->configListener = $configListener;
    }

    public function getConfigListener()
    {
        return $this->configListener;
    }

    public function setEnabled($flag)
    {
        $this->enabled = (bool)$flag;
    }

    public function isEnabled()
    {
        return $this->enabled;
    }

    public function setMaxAge($value)
    {
        $this->maxAge = ($value >= 0) ? $value : null;
    }

    public function getMaxAge()
    {
        return $this->maxAge;
    }

    public function setSMaxAge($value)
    {
        $this->sMaxAge = ($value >= 0) ? $value : null;
    }

    public function getSMaxAge()
    {
        return $this->sMaxAge;
    }

    public function setUseModifiedSince($useModifiedSince)
    {
        $this->useModifiedSince = $useModifiedSince;
    }

    public function getUseModifiedSince()
    {
        return $this->useModifiedSince;
    }

    public function setMustRevalidate($mustRevalidate)
    {
        $this->mustRevalidate = $mustRevalidate;
    }

    public function istMustRevalidate()
    {
        return $this->mustRevalidate;
    }

    public function merge(array $data)
    {
        foreach ($data as $name => $value) {
            switch ($name) {
                case 'max-age':
                    $this->setMaxAge($value);
                    break;

                case 's-maxage':
                    $this->setSMaxAge($value);
                    break;

                case 'must-revalidate':
                    $this->setMustRevalidate($value);
                    break;
            }
        }
    }
}