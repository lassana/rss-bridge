<?php
class RrbByBridge extends BridgeAbstract {

	const MAINTAINER = 'lassana';
	const NAME = 'Новости для клиентов Банка РРБ';
	const URI = 'https://www.rrb.by';
	const DESCRIPTION = 'Новости о деятельности РРБ-Банка: изменения в режиме работы, важные события, изменениях в тарифах и другие важные сведения.';
	const CACHE_TIMEOUT = 0;

	public function collectData() {
		$mainPageUrl = self::URI . '/presscentr/news';
		$html = getSimpleHTMLDOM($mainPageUrl)
			or returnServerError('Could not request rrb.by.');

		foreach($html->find('div.content-list-item') as $element) {
			$item = array();
			$item['uid'] = 'urn:sha1:' . hash('sha1', $element->find('div.content-list-item-title', 0)->first_child()->innertext);
			$item['title'] = $element->find('div.content-list-item-title', 0)->first_child()->innertext;
			$item['timestamp'] = DateTime::createFromFormat(
				'Y / m / d',
				$element->find('div.content-list-item-date', 0)->innertext
				)->getTimestamp();
			$item['uri'] = self::URI . $element->find('div.content-list-item-title', 0)->first_child()->href;

			$this->items[] = $item;
		}
	}
}
