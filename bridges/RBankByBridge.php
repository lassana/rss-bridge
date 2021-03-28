<?php
class RBankByBridge extends BridgeAbstract {

	const MAINTAINER = 'lassana';
	const NAME = 'RBank.by Новости';
	const URI = 'https://rbank.by';
	const DESCRIPTION = 'Gets the service news from RBank.by website.';
	const CACHE_TIMEOUT = 0;

	public function collectData(){
		$mainPageUrl = self::URI . '/news/';
		$html = getSimpleHTMLDOM($mainPageUrl)
			or returnServerError('Could not request rbank.by.');

		foreach($html->find('div.media-old') as $element){
			$item = array();
			$item['title'] = $element->find('div.title', 0)->first_child()->innertext;
			$item['timestamp'] = DateTime::createFromFormat(
				'Y-m-d',
				$element->find('div.date', 0)->first_child()->getAttribute('datetime')
				)->getTimestamp();
			$item['uri'] = self::URI . $element->find('div.title', 0)->first_child()->href;

			$imageNode = $element->find('img.img', 0);
			if($imageNode){
				$item['enclosures'] = array(
					self::URI . $imageNode->getAttribute('src')
				);
			}

			$item['content'] = $element->find('div.text', 0)->first_child()->outertext;

			$this->items[] = $item;
		}
	}
}