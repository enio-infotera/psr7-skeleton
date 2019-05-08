<?php

namespace App\Domain\User;

use App\Domain\Service\DomainServiceInterface;
use Odan\Session\SessionInterface;
use Symfony\Component\Translation\Translator;

/**
 * DTO.
 */
final class Locale implements DomainServiceInterface
{
    /** @var string Locale path */
    public $localePath;

    /** @var SessionInterface */
    private $session;

    /** @var Translator */
    private $translator;

    /**
     * Constructor.
     *
     * @param Translator $translator The translator
     * @param SessionInterface $session The session
     * @param string $localePath The directory with the locals
     */
    public function __construct(Translator $translator, SessionInterface $session, string $localePath)
    {
        $this->translator = $translator;
        $this->session = $session;
        $this->localePath = $localePath;
    }

    /**
     * Get local.
     *
     * @return string|null
     */
    public function getLocale(): ?string
    {
        return $this->session->get('locale');
    }

    /**
     * Get text domain.
     *
     * @return string|null
     */
    public function getDomain(): ?string
    {
        return $this->session->get('domain');
    }

    /**
     * Change user session locale.
     *
     * @param string $locale e.g. en_US
     * @param string $domain e.g. messages
     *
     * @return bool Status
     */
    public function setLanguage(string $locale, string $domain = 'messages'): bool
    {
        $this->setLocale($locale);
        $this->setDomain($domain);
        $this->setTranslatorLocale($locale, $domain);

        return true;
    }

    /**
     * Set locale.
     *
     * @param string $locale locale
     *
     * @return void
     */
    private function setLocale(string $locale): void
    {
        $this->session->set('locale', $locale);
    }

    /**
     * Set text domain.
     *
     * @param string $domain domain
     *
     * @return void
     */
    private function setDomain(string $domain): void
    {
        $this->session->set('domain', $domain);
    }

    /**
     * Set locale.
     *
     * @param string $locale locale
     * @param string $domain domain
     *
     * @return void
     */
    private function setTranslatorLocale(string $locale, string $domain = 'messages'): void
    {
        $moFile = sprintf('%s/%s_%s.mo', $this->localePath, $locale, $domain);
        $this->translator->addResource('mo', $moFile, $locale, $domain);
        $this->translator->setLocale($locale);
    }
}
