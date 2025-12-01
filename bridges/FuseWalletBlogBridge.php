<?php
class FuseWalletBlogBridge extends BridgeAbstract {

	const MAINTAINER = 'lassana';
	const NAME = 'Fuse Wallet Blog';
	const URI = 'https://fusewallet.com';
	const DESCRIPTION = 'Fuse is the personal finance app powered by stablecoin rails.';
	const CACHE_TIMEOUT = 0; // 0 min

	public function collectData() {
		$mainPageUrl = self::URI;

		$html = getSimpleHTMLDOM($mainPageUrl . '/blog')
			or returnServerError('Could not request fusewallet.com.');
		$limit = 0;
		
		$section = $html->find('section[data-framer-name="All"]', 0);

		foreach ($section->children() as $element) {
			if (isset($element->tag) && strtolower($element->tag) === 'a') {
				$item = array();

				$href = (string) $element->href;
				if (isset($href[0]) && $href[0] === '.') {
					$href = substr($href, 1);
				}
				$item['uri'] = $mainPageUrl . $href;

				$item['title'] = $element->find('h6', 0)->innertext;

				$imageNode = $element->find('img', 0);
				if($imageNode) {
					$item['enclosures'] = array(
						html_entity_decode($imageNode->getAttribute('src'), ENT_QUOTES | ENT_HTML5, 'UTF-8')
					);
				}

				$subHeader = $element->find('[data-framer-component-type="RichTextContainer"]', 0)->parent();
				if ($subHeader) {
					$item['categories'] = [$subHeader->children()[0]->find('p', 0)->innertext];

					$dateNode = $subHeader->children()[1]->find('p', 0);
					if ($dateNode && $dateNode->innertext) {
						$item['timestamp'] = strtotime($dateNode->innertext);
					}
				}

				$this->items[] = $item;
			}
		}
	}

	public function getIcon() {
		return 'https://framerusercontent.com/images/44LSPEvGKRshhUCwnMujygx8Geo.png';
	}
}