<?php
class BnbByBridge extends BridgeAbstract {

	const MAINTAINER = 'lassana';
	const NAME = 'Новости БНБ-Банка';
	const URI = 'https://bnb.by';
	const DESCRIPTION = 'Gets the bank news from bnb.by website.';
	const CACHE_TIMEOUT = 0;
	const PARAMETERS = array(
		'News' => array(
			'fullContent' => array(
				'name' => 'Включать содержимое',
				'type' => 'checkbox',
				'title' => 'Если выбрано, содержимое уведомлений вставляется в поток (работает медленно)'
			)
		)
	);

	public function collectData(){
		$fullContent = $this->getInput('fullContent') == 'on';

		$mainPageUrl = self::URI . '/chtoby-znali/rss-lenta/';
		$html = getSimpleHTMLDOM($mainPageUrl)
			or returnServerError('Could not request bnb.by.');

		foreach($html->find('p.news-item') as $element){
			$item = array();
			$item['title'] = $element->find('a', 0)->first_child()->innertext;
			$item['timestamp'] =
				 DateTime::createFromFormat(
				' d.m.Y',
				str_replace(' ', '', $element->find('span.news-date-time', 0)->innertext)
				)->getTimestamp()
				;
			$item['uri'] = self::URI . $element->find('a', 0)->href;

			if($fullContent){
				$itemHtml = getSimpleHTMLDOM($item['uri']);
				if($itemHtml){
					$item['_content'] = $itemHtml->find('div.news-detail', 0)->innertext;
				}
			}

			$this->items[] = $item;
		}
	}
}