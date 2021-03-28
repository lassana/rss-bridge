<?php
class WikilinkByBridge extends BridgeAbstract {

	const MAINTAINER = 'lassana';
	const NAME = 'Wikilink.by Новости';
	const URI = 'http://wikilink.by';
	const DESCRIPTION = 'Gets the service news from Wikilink.by website.';
	const CACHE_TIMEOUT = 0;
	const PARAMETERS = array(
		'News' => array(
			'source' => array(
				'name' => 'Источник',
				'type' => 'list',
				'title' => 'Источник для ленты',
				'values' => array(
					'Новости' => 'news',
					'Работы на сетях связи' => 'incidents'
				),
				'title' => 'Определяет какие данные будут использованы',
				'defaultValue' => 'news'
			)
		)
	);

	public function collectData(){
		$incidents = $this->getInput('source') == 'incidents';

		$mainPageUrl = self::URI;
		if($incidents){
			$mainPageUrl .= '/category/works/';
		} else{
			$mainPageUrl .= '/category/news/';
		}
		$html = getSimpleHTMLDOM($mainPageUrl)
			or returnServerError('Could not request Wikilink.');

		$cursor = $html->find('div.news_list', 0)->first_child();
		while(true){
			if($cursor == NULL){
				break;
			}

			$item = array();
			$item['uri'] = $cursor->find('a', 0)->href;
			$item['title'] = $cursor->find('a', 0)->innertext;

			preg_match('/\d{2}\.\d{2}\.\d{4}/', $cursor->outertext, $dateFound);
			$item['timestamp'] = DateTime::createFromFormat(
				'd.m.Y',
				$dateFound[0]
				)->getTimestamp();

			$cursor = $cursor->next_sibling();
			$item['content'] = $cursor->outertext;

			$this->items[] = $item;

			$cursor = $cursor->next_sibling()->next_sibling();
		}
	}
}