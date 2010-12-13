<?php /** @package SQLSolution_Test */

/**
 * Tests the SQL Solution's ParseSafeMarkup method
 *
 * @package SQLSolution_Test
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2001-2010
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 * @link http://www.analysisandsolutions.com/software/sql/sql.htm
 */
class SQLSolution_Test_General_ParseSafeMarkupTest extends SQLSolution_Test_General {
	public function testParseSafeMarkup() {
		$return = $this->sql->ParseSafeMarkup('::p::p::/p::');
		$this->assertEquals('<p>p</p>', $return);

		$return = $this->sql->ParseSafeMarkup('::ul::ul::/ul::');
		$this->assertEquals('<ul>ul</ul>', $return);

		$return = $this->sql->ParseSafeMarkup('::ol::ol::/ol::');
		$this->assertEquals('<ol>ol</ol>', $return);

		$return = $this->sql->ParseSafeMarkup('::li::li::/li::');
		$this->assertEquals('<li>li</li>', $return);

		$return = $this->sql->ParseSafeMarkup('::dl::dl::/dl::');
		$this->assertEquals('<dl>dl</dl>', $return);

		$return = $this->sql->ParseSafeMarkup('::dt::dt::/dt::');
		$this->assertEquals('<dt>dt</dt>', $return);

		$return = $this->sql->ParseSafeMarkup('::dd::dd::/dd::');
		$this->assertEquals('<dd>dd</dd>', $return);

		$return = $this->sql->ParseSafeMarkup('::b::b::/b::');
		$this->assertEquals('<b>b</b>', $return);

		$return = $this->sql->ParseSafeMarkup('::i::i::/i::');
		$this->assertEquals('<i>i</i>', $return);

		$return = $this->sql->ParseSafeMarkup('::code::code::/code::');
		$this->assertEquals('<code>code</code>', $return);

		$return = $this->sql->ParseSafeMarkup('::sup::sup::/sup::');
		$this->assertEquals('<sup>sup</sup>', $return);

		$return = $this->sql->ParseSafeMarkup('::pre::pre::/pre::');
		$this->assertEquals('<pre>pre</pre>', $return);

		$return = $this->sql->ParseSafeMarkup('::tt::tt::/tt::');
		$this->assertEquals('<tt>tt</tt>', $return);

		$return = $this->sql->ParseSafeMarkup('::em::em::/em::');
		$this->assertEquals('<em>em</em>', $return);

		$return = $this->sql->ParseSafeMarkup('::blockquote::blockquote::/blockquote::');
		$this->assertEquals('<blockquote>blockquote</blockquote>', $return);

//		$return = $this->sql->ParseSafeMarkup('::bei::bei::/bei::');
//		$this->assertEquals('::bei::bei::/bei::', $return);


		$return = $this->sql->ParseSafeMarkup('::br::');
		$this->assertEquals('<br />', $return);

		$return = $this->sql->ParseSafeMarkup('::hr::');
		$this->assertEquals('<hr />', $return);


		$return = $this->sql->ParseSafeMarkup('::amp::');
		$this->assertEquals('&amp;', $return);

		$return = $this->sql->ParseSafeMarkup('::frac34::');
		$this->assertEquals('&frac34;', $return);

		$return = $this->sql->ParseSafeMarkup('::AElig::');
		$this->assertEquals('&AElig;', $return);

		$return = $this->sql->ParseSafeMarkup('::abcdefg::');
		$this->assertEquals('::abcdefg::', $return);

		$return = $this->sql->ParseSafeMarkup('::foo987::');
		$this->assertEquals('::foo987::', $return);


		$return = $this->sql->ParseSafeMarkup('::1234::');
		$this->assertEquals('&#1234;', $return);

		$return = $this->sql->ParseSafeMarkup('::1::');
		$this->assertEquals('::1::', $return);

		$return = $this->sql->ParseSafeMarkup('::12345::');
		$this->assertEquals('::12345::', $return);


		$return = $this->sql->ParseSafeMarkup('http://foo.com');
		$this->assertEquals('<a href="http://foo.com">http://foo.com</a>', $return);

		$return = $this->sql->ParseSafeMarkup('https://foo.com');
		$this->assertEquals('<a href="https://foo.com">https://foo.com</a>', $return);

		$return = $this->sql->ParseSafeMarkup('ftp://foo.com');
		$this->assertEquals('<a href="ftp://foo.com">ftp://foo.com</a>', $return);

		$return = $this->sql->ParseSafeMarkup('gopher://foo.com');
		$this->assertEquals('<a href="gopher://foo.com">gopher://foo.com</a>', $return);

		$return = $this->sql->ParseSafeMarkup('news:foo.com');
		$this->assertEquals('<a href="news:foo.com">news:foo.com</a>', $return);

		$return = $this->sql->ParseSafeMarkup('mailto:foo.com');
		$this->assertEquals('<a href="mailto:foo.com">mailto:foo.com</a>', $return);

		$return = $this->sql->ParseSafeMarkup('bogus:foo.com');
		$this->assertEquals('bogus:foo.com', $return);


		$return = $this->sql->ParseSafeMarkup('::a::http://foo.com::a::bar::/a::');
		$this->assertEquals('<a href="http://foo.com">bar</a>', $return);

		$return = $this->sql->ParseSafeMarkup('::a::https://foo.com::a::bar::/a::');
		$this->assertEquals('<a href="https://foo.com">bar</a>', $return);

		$return = $this->sql->ParseSafeMarkup('::a::ftp://foo.com::a::bar::/a::');
		$this->assertEquals('<a href="ftp://foo.com">bar</a>', $return);

		$return = $this->sql->ParseSafeMarkup('::a::gopher://foo.com::a::bar::/a::');
		$this->assertEquals('<a href="gopher://foo.com">bar</a>', $return);

		$return = $this->sql->ParseSafeMarkup('::a::news:foo.com::a::bar::/a::');
		$this->assertEquals('<a href="news:foo.com">bar</a>', $return);

		$return = $this->sql->ParseSafeMarkup('::a::mailto:foo.com::a::bar::/a::');
		$this->assertEquals('<a href="mailto:foo.com">bar</a>', $return);

		$return = $this->sql->ParseSafeMarkup('::a::bogus:foo.com::a::bar::/a::');
		$this->assertEquals('::a::bogus:foo.com::a::bar::/a::', $return);

		$return = $this->sql->ParseSafeMarkup('::a::bogus:foo.com::a::oygevalt::/a::');
		$this->assertEquals('::a::bogus:foo.com::a::oygevalt::/a::', $return);
	}
}
