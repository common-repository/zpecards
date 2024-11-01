<?php

require_once('../zp_ecard_functions.php');

$post['Width'] = $_POST['Width'];
$post['Height'] = $_POST['Height'];
$post['SortBy'] = $_POST['SortBy'];

$post['Search']= $_POST['Search'];

$url = 'http://zetaprints.com?page=api-search';
$res[] = $post;
$res[] = $url;

$thePage = getTemplate($post, $url, false);

$fp = fopen('test.xml' , 'w');
fwrite($fp, $thePage);
fclose($fp);

if (file_exists('test.xml')) {
    $xml = simplexml_load_file('test.xml');

    $items = $xml->channel->item;
        echo '
<div id="storeContent">
	<div id="frameTop">
	</div>

	<div id="contentMask" class="deptBGSettings">
		<div id="deptContent">
			<table cellspacing="0" cellpadding="0" border="0" id="deptImgs">
				<tr>
';
    foreach ($items as $key=>$item) {
echo '
					<td>
						<div class="imgcol">
							<div class="titlediv">
								<span class="titletxt">' . $item->title . '</span>
							</div>
							<div class="selectdiv">
								<center>
									<select id="' . $item->id . '" onchange="selectOpt(this.id,\'' . $item->cid . '\',this.options[this.selectedIndex].value,this)" class="selectdd" title="Select">
										<option class="selectop" value="0">Choose...</option>
										<option class="selectop" value="1">Try template</option>
										<option class="selectop" value="2">Use template</option>
										<option class="selectop" value="3">View catalog</option>
										<option class="selectop" value="4">Use catalog</option>
									</select>
								</center>
							</div>
							<div class="thumbdiv">
								<center>
									<a href="javascript:tryTemplate(\'' . $item->id . '\')"><img class="thumbimg" alt="' . $item->title . '" src="' . $item->thumbnail . '" title="Click to try this template" class="thumb"/></a>
								</center>
							</div>
							<div class="tagsdiv" style="display:none">
								<b>JPEG, GIF, PNG</b>(400x400)
							</div>
						</td>
					</div>
';

    }
    echo '
				</tr>
			</table>
		</div>
	</div>
</div>
';

} else {
    exit('Failed to open test.xml.');
}

