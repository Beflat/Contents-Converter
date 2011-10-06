<?php

$content = "<script><!--  function a () { alert('aaaa'); } --> <html> <!-- bbb; --><head>";

stripComment($content);
echo $res = $content;



function stripComment(&$content) {
  
  $beginPos = mb_strpos($content, '<!--');
  if($beginPos === false) {
    return false;
  }
  
  $endPos = mb_strpos($content, '-->', $beginPos+3);
  if($endPos === false) {
    return false;
  }

  $newContent = mb_substr($content, 0, $beginPos);
  $newContent .= mb_strcut($content, $endPos+3);
  $content = $newContent;
  
  return stripComment($content);
}