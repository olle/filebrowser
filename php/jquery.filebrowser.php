<?php
/*
 * The MIT License
 * 
 * Copyright (c) 2008-2009 Olle Törnström studiomediatech.com
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @author  Olle Törnström olle[at]studiomediatech[dot]com
 * @since   2009-09-27
 */

$folders = array();
$files = array();

$dir = urldecode(strval($_POST['dir'])) . '/';
$dir = str_replace('//', '/', $dir);

if (file_exists($dir)) {
	$dirItems = array_filter(scandir($dir), 'filter');
	natcasesort($dirItems);
	foreach ($dirItems as $item) {
		if (isCleanDir($dir, $item)) {
			$folders[] = new Folder($dir, $item);
		} else if (isCleanFile($dir, $item)) {
			$ext = preg_replace('/^[^.]*\./', '', $item);
			$files[] = new File($dir, $item, $ext);
		}
	}
}

function filter($var) {
	$filter = array('.', '..');
	return !in_array($var, $filter);
}

function isCleanDir($dir, $fileItem) {
	return file_exists($dir . $fileItem) && is_dir($dir . $fileItem);
}

function isCleanFile($dir, $fileItem) {
	return file_exists($dir . $fileItem) && !is_dir($dir . $fileItem);
}

class Fileitem {
	public $dir;
	public $name;
	public function __construct($dir, $name) {
		$this->dir = $dir;
		$this->name = trim($name);
	}
}
class File extends Fileitem {
	public $extension;
	public function __construct($dir, $name, $extension) {
		parent::__construct($dir, $name);
		$this->extension = $extension;
	}
}
class Folder extends Fileitem {
	public $isEmpty;
	public function __construct($dir, $name) {
		parent::__construct($dir, $name);
		$this->isEmpty = count(scandir($dir . $name)) < 3;
	}
}

?><ul rel="<?php echo htmlentities($dir) ?>" class="node">
<?php foreach ($folders as $item): ?>
	<li class="folder<?php echo $item->isEmpty ? ' empty' : '' ?>" rel="<?php echo $item->dir . $item->name ?>">
		<span><?php echo $item->name ?></span>
	</li>
<?php endforeach ?>
<?php foreach ($files as $item): ?>
	<li class="file <?php echo $item->extension ?>" rel="<?php echo $item->dir . $item->name ?>">
		<span><?php echo $item->name ?></span>
	</li>
<?php endforeach ?>
</ul>