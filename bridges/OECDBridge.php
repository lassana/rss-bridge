<?php
class OECDBridge extends BridgeAbstract {

	const MAINTAINER = 'lassana';
	const NAME = 'OECD News';
	const URI = 'https://www.oecd.org/';
	const DESCRIPTION = 'Gets the news articles from the specified OECD page.';
	const CACHE_TIMEOUT = 0;
	const PARAMETERS = array(
		'Source' => array(
			'uriPath' => array(
				'name' => 'URI path',
				'type' => 'text',
				'title' => 'URI path that comes after www.oecd.org domain, for example /countries/belarus',
				'required' => true
			)
		)
	);

	public function collectData(){
		$uriPath = $this->getInput('uriPath');

		$mainPageUrl = self::URI . $uriPath;

		$html = getSimpleHTMLDOM($mainPageUrl)
			or returnServerError('Could not request OECD');

		$limit = 0;

		foreach($html->find('li.item') as $element) {
			if($limit < 10) {
				$item = array();

				$uri = trim($element->find('h4', 0)->find('a', 0)->getAttribute('href'));
				if(filter_var($uri, FILTER_VALIDATE_URL)) {
					$item['uri'] = $uri;
				} else {
					$item['uri'] = self::URI . $uri;
				}
				$item['title'] = trim($element->find('h4', 0)->find('a', 0)->innertext);
				$item['timestamp'] = DateTime::createFromFormat(
					'j-F-Y',
					trim($element->find('p.date', 0)->innertext)
					)->getTimestamp();
				$item['content'] = $element->find('.content', 0)->outertext;

				$enclosures = [];
				foreach($element->find('ul.linkList') as $element2) {
					foreach($element2->find('li') as $element3) {
						$element3Uri = $element3->find('a', 0);
						if($element3Uri) {
							$enclosures[] = self::URI . trim($element3Uri->getAttribute('href'));
						}
					}
				}
				$item['enclosures'] = $enclosures;

				$item['categories'] = explode(
					', ',
					trim($element->find('p.infos', 0)->find('em', 0)->innertext));

				$item['uid'] = 'urn:sha1:' . hash('sha1', $item['uri']);

				$this->items[] = $item;
				$limit++;
			}
		}
	}
}
