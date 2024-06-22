<?php
class PkoBankPolskiBridge extends BridgeAbstract {

	const MAINTAINER = 'lassana';
	const NAME = 'PKO Bank Polski';
	const URI = 'https://www.pkobp.pl';
	const DESCRIPTION = 'PKO Bank Polski: Aktualności';
	const CACHE_TIMEOUT = 5; // 5 min
	const PARAMETERS = array(
		'News' => array(
			'categories' => array(
				'name' => 'Kategorie',
				'type' => 'list',
				'title' => 'Kategorie',
				'values' => array(
					'Wszystkie' => '0',
					'Bankowość elektroniczna' => '4',
					'Bezpieczeństwo' => '3',
					'Kariera' => '7'
				),
				'defaultValue' => '0'
			),
			'customers' => array(
				'name' => 'Klienci',
				'type' => 'list',
				'title' => 'Klienci',
				'values' => array(
					'Wszystkie' => '0',
					'Bankowość prywatna' => '8',
					'Dzieci' => '7',
					'Firmy i przedsiębiorstwa' => '2',
					'IKO' => '6',
					'Indywidualni' => '1',
					'iPKO' => '4',
					'iPKO biznes' => '5',
					'Korporacje i samorządy' => '3'
				),
				'defaultValue' => '0'
			)
		)
	);

	public function collectData() {
		$categories = $this->getInput('categories');
		$customers = $this->getInput('customers');

		$mainPageUrl = self::URI . '/api/news/items?page_size=8&page_id=649';
		if ($categories != '0') {
			$mainPageUrl .= '&categories=' . $categories;
		}
		if ($customers != '0') {
			$mainPageUrl .= '&customers=' . $customers;
		}
		$mainPageUrl .= '&variant=contents';

		$jsonContent = getContents($mainPageUrl);
		$json = Json::decode($jsonContent);
		$limit = 0;

		foreach($json['results'] as $element) {
			if($limit < 10) {
				$item = array();
				$itemUrl = self::URI . $element['path'];
				$item['uid'] = 'urn:sha1:' . hash('sha1', $itemUrl);
				$item['uri'] = $itemUrl;
				$item['title'] = $element['snippet']['title']['text'];
				$content = '<div>';
				$label = $element['snippet']['label'];
				$labelColor = $element['snippet']['label_color'];
				if (!empty($label) && $labelColor != '#F2F2F2') {
					$content .= '<span style="'
						. 'font-weight: bold;'
						. 'color: #fff;'
						. 'background-color: ' . $labelColor . ';'
						. 'border-radius: 4px;'
						. 'padding: 4px;'
						. 'margin-right: 4px;
						. ">' . $label . '</span>';
				}
				$content .= '<span> '
					. $element['snippet']['lead']
					. '</span>';
				$content .= '</div>';
				$item['content'] = $content;
				$item['timestamp'] = DateTime::createFromFormat(
					DateTime::ATOM,
					$element['snippet']['publication_date']
					)->getTimestamp();

				$this->items[] = $item;
				$limit++;
			}
		}
	}

	public function getIcon() {
		return static::URI . '/static/redesign/_front/_img/_layout/favicon_new.png';
	}
}
