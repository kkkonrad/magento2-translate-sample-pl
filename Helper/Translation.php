<?php

namespace Kkkonrad\Console29\Helper;

use \Magento\Framework\Phrase\Renderer\TranslateFactory as TranslateRendererFactory;
use Magento\Framework\TranslateInterface;
use \Magento\Framework\TranslateInterfaceFactory as TranslateFactory;


class Translation
{

    /**
     * @var TranslateRendererFactory
     */
    private $rendererFactory;

    /**
     * @var TranslateInterfaceFactory
     */
    private $translateFactory;

    /**
     * @var array || TranslateInterface[]
     */
    private $translatorPool = [];

    /**
     * @param TranslateRendererFactory $rendererFactory
     * @param TranslateFactory $translateFactory
     */
    public function __construct(TranslateRendererFactory $rendererFactory, TranslateFactory $translateFactory)
    {
        $this->rendererFactory = $rendererFactory;
        $this->translateFactory = $translateFactory;
    }

    /**
     * Translate the given string in the correct local.
     *
     * @param string $string
     * @param string $langCode
     * @return string
     */
    public function translateByLangCode(string $string, string $langCode): string
    {
        $translator = $this->getTranslator($langCode);
        $orgRenderer = \Magento\Framework\Phrase::getRenderer();

        $renderer = $this->rendererFactory->create(['translator' => $translator]);
        // inject translation render
        \Magento\Framework\Phrase::setRenderer($renderer);

        //translate the given string
        $phrase = new \Magento\Framework\Phrase($string);
        $translation = (string)$phrase;

        // reset the renderer to original
        \Magento\Framework\Phrase::setRenderer($orgRenderer);

        return $translation;
    }

    /**
     * Return the translator instance by correct language.
     *
     * @param string $langCode
     * @return mixed
     */
    private function getTranslator(string $langCode)
    {
        if (!isset($this->translatorPool[$langCode])) {
            /** @var TranslateInterface $translator */
            $translator = $this->translateFactory->create();
            $translator->setLocale($langCode);
            $translator->loadData();
            $this->translatorPool[$langCode] = $translator;
        }

        return $this->translatorPool[$langCode];
    }
}
