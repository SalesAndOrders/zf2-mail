<?php
namespace Mailing;

use Zend\I18n\Translator\Translator;
use Zend\Mail as ZendMail;
use Zend\Stdlib\ArrayUtils;

/**
 * Class Config
 * @package Mail
 */
class Config
{
    const TYPE_HTML = 'html';
    const TYPE_PLAIN = 'plain';

    /**
     * @var array
     */
    protected $config;

    /**
     * @var Translator|null
     */
    protected $translator;

    /**
     * Config constructor.
     * @param $config
     * @param Translator|null $translator
     */
    public function __construct($config, Translator $translator = null)
    {
        $this->setConfig($config);
        $this->translator = $translator;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param \Traversable|array $config
     * @return $this
     */
    public function setConfig($config)
    {
        $this->config = ArrayUtils::iteratorToArray($config);
        return $this;
    }

    /**
     * @param string $emailAlias
     * @param string $type
     * @return null|string
     */
    public function getTemplate($emailAlias, $type = self::TYPE_PLAIN)
    {
        $email = $this->getEmailAlias($emailAlias);
        if (isset($email['template'][$type])) {
            return $email['template'][$type];
        }
        return null;
    }

    /**
     * @param string $emailAlias
     * @return array|null
     */
    public function getFrom($emailAlias)
    {
        $email = $this->getEmailAlias($emailAlias);
        if (isset($email['from']) && isset($this->config['from'][$email['from']])) {
            $emailAddress = $this->getFromEmail($email['from']);
            $name = isset($this->config['from'][$email['from']]['name']) ? $this->config['from'][$email['from']]['name'] : null;
            return array('name' => $name, 'email' => $emailAddress);
        }
        return null;
    }

    /**
     * @param string $fromAlias
     * @return null|string
     */
    public function getFromEmail($fromAlias)
    {
        if (isset($this->config['from'][$fromAlias])) {
            $from = $this->config['from'][$fromAlias];
            if (is_string($from['email']) && strpos($from['email'], '@') !== false) {
                return $from['email'];
            }
            if (!isset($from['domain'])) {
                $from['domain'] = 'default';
            }
            $domain = $this->config['domains'][$from['domain']];
            return $from['email'] . '@' . $domain;
        }
        return null;
    }



    /**
     * @param string $emailAlias
     * @param string $type
     * @return null|string
     */
    public function getLayoutTemplate($emailAlias, $type = self::TYPE_PLAIN)
    {
        $email = $this->getEmailAlias($emailAlias);
        if (isset($email['layout']) && isset($this->config['layouts'][$email['layout']][$type])) {
            return $this->config['layouts'][$email['layout']][$type];
        }
        return null;
    }

    /**
     * @param string $emailAlias
     * @return null|string
     */
    public function getSubject($emailAlias)
    {
        $email = $this->getEmailAlias($emailAlias);
        if (isset($email['subject']) && !empty($this->translator)) {
            return $this->translator->translate($email['subject']);
        } elseif (isset($email['subject'])) {
            return $email['subject'];
        } else {
            return null;
        }
    }

    /**
     * @param string $emailAlias
     * @return null|string
     */
    public function getSubjectTemplate($emailAlias)
    {
        $email = $this->getEmailAlias($emailAlias);
        if (isset($email['template']['subject'])) {
            return $email['template']['subject'];
        }
        return null;
    }

    /**
     * @return ZendMail\Transport\TransportInterface
     */
    public function getTransport()
    {
        if (!isset($this->config['transport'])) {
            $this->config['transport'] = array();
        }
        if (!isset($this->config['transport']['type'])) {
            $this->config['transport']['type'] = 'sendmail';
        }
        if (!isset($this->config['transport']['options'])) {
            $this->config['transport']['options'] = array();
        }

        $type = strtolower($this->config['transport']['type']);
        $options = $this->config['transport']['options'];

        switch ($type) {
            case 'sendmail':
                return new ZendMail\Transport\Sendmail($options);
                break;

            case 'smtp':
                $options = new ZendMail\Transport\SmtpOptions($options);
                return new ZendMail\Transport\Smtp($options);
                break;

            case 'file':
                $options = new ZendMail\Transport\FileOptions($options);
                return new ZendMail\Transport\File($options);
                break;
        }

        return null;
    }

    /**
     * @param string $alias
     * @return array
     * @throws Exception\InvalidArgumentException
     */
    protected function getEmailAlias($alias)
    {
        if (!isset($this->config['mails'][$alias])) {
            throw new Exception\InvalidArgumentException('Email alias '. $alias . ' is not defined');
        }

        $result = $this->config['mails'][$alias];
        if (!is_array($result)) {
            $result = array($result);
        }

        if (!isset($result['layout'])) {
            $result['layout'] = 'default';
        }
        if (!isset($result['from'])) {
            $result['from'] = 'default';
        }
        if (!isset($result['template'])) {
            $result['template'] = array();
        }
        return $result;
    }
}
