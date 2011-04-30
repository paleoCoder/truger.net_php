<?php
/*******************
meaning full comment of the functionality of the php

"to make egg roll, push it"

********************/

//include db info
include("dbConnect.php");

//for testing
$debug = False;

$mysqli = getDB();

//check connection
if(mysqli_connect_errno()){
   printf("Site down. Please contact admin@truger.net");
   printf("Connection failed: %s\n", mysqli_connect_error());
} 
else { //continue loading page

   //TODO: parse post to get the page id, default to 1
   $pageInfo = getPageInfo($mysqli, 1);

   $pageArticles = getPageArticles($mysqli, $pageInfo['pageID']);

   $articlesHTML = formatArticles($pageArticles);

   mysqli_close($mysqli);
}  

/*
retrieve all the info from the db for the page
*/
function getPageInfo($db, $pageID){
   $sql_page = "select * from page where pageID = $pageID";
   $res_page = mysqli_query($db, $sql_page);

   if($res_page){
      // get the page info
      while($pageArray = mysqli_fetch_array($res_page, MYSQLI_ASSOC)){
      
         //save the page info into an array
         $pageInfo['pageID'] = $pageArray['pageID'];
         $pageInfo['title'] = $pageArray['title'];
         $pageInfo['analytics'] = $pageArray['analytics'];
         $pageInfo['header'] = $pageArray['header'];
         $pageInfo['header_blurb'] = $pageArray['header_blurb'];
         $pageInfo['page_desc'] = $pageArray['page_desc'];
         $pageInfo['copyright'] = $pageArray['copyright'];

         if($debug){
            echo "pageID ".$pageInfo['pageID']."<br />";
            echo "title ".$pageInfo['title']."<br />";
            //echo "analytics ".$pageInfo['analytics']."<br />";
            echo "header ".$pageInfo['header']."<br />";
            echo "header_blurb".$pageInfo['header_blurb']."<br />";
            echo "page_desc".$pageInfo['page_desc']."<br />";
            echo "copyright".$pageInfo['copyright']."<br />";
         } 

      }//end while there are more page results

   } else {
      printf("Query for the page $pageID failed: %s\n", mysqli_error($mysqli));
   }

   mysqli_free_result($res_page);

   return $pageInfo;
}

/*

*/
function getPageArticles($db, $pageID){

	// gett all page articles that have a sort order > 0 that are associated with the given pageID
   $sql = "select * from page_article where pageID = $pageID and sort_order > 0 order by sort_order";
   $sql = 'SELECT *'
        . ' FROM page_article'
        . " WHERE `pageID` = $pageID"
        . ' and `sort_order` > 0'
        . ' ORDER BY sort_order LIMIT 0, 30 '; 
   $res = mysqli_query($db, $sql);
   
   if($res){
      //save articles to array
      while($articleArray = mysqli_fetch_array($res, MYSQLI_ASSOC)){
         $pageArticles[] = array(
               'articleID' => $articleArray['articleID'],
               'title' => $articleArray['title'],
               'url' => $articleArray['url'],
               'desc' => $articleArray['desc'],
               'sort_order' => $articleArray['sort_order']
            );

      }// end while there are more articles   
   }//end if query result
   else {
      printf("Query for the articles for pageID: $pageID failed: %s\n", mysqli_error($mysqli));
   }

   if($debug){
      echo "articles <br />";   
      foreach ($pageArticles as $article){
         foreach($article as $k => $v){
            echo "k $k v $v <br/>";
         }
         echo "---------<br />";
      }
   }

   return $pageArticles;
}

/*

*/
function formatArticles($pageArticles){

   if($debug){
      foreach ($pageArticles as $article){
         // foreach($article as $k => $v){
         //    echo "k $k v $v <br/>";
         // }
         // echo "---------<br />";
         echo "articleID: ".$article['articleID']."<br/>";
         echo "title: ".$article['title']."<br/>";
         echo "url: ".$article['url']."<br/>";
         echo "desc: ".$article['desc']."<br/>";
         echo "sort_order: ".$article['sort_order']."<br/>";
      }
   }

   $articles = "<ul>";

   foreach ($pageArticles as $article){
      $articles .= 
         "<li>
            <article>
               <header>
                  <a href=\"".$article['url']."\" title=\"".$article['title']."\">".$article['title']."</a>
               </header>
               <p>".$article['desc']."</p>
            </article>
         </li>";
   }

   $articles .= "</ul>";

   return $articles;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="utf-8" />
   <title><?php echo $pageInfo['title']; ?></title>
   <link rel="stylesheet" href="css/main.css" type="text/css" />
   
   <!--[if IE]>
   <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
   <!--[if lte IE 7]>
   <script src="js/IE8.js" type="text/javascript"></script><![endif]-->
   <!--[if lt IE 7]>
   <link rel="stylesheet" type="text/css" media="all" href="css/ie6.css"/><![endif]-->
   
   <script type="text/javascript">

     var _gaq = _gaq || [];
     _gaq.push(['_setAccount', 'UA-19819922-1']);
     _gaq.push(['_trackPageview']);

     (function() {
       var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
       ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
       var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
     })();

   </script>
   
</head>

<body>
   <header class=banner>
      <h1><?php echo $pageInfo['header'];?><strong><?php echo $pageInfo['header_blurb']; ?></strong></h1>
   </header>
   <section>
      <p><?php echo $pageInfo['page_desc'];?></p>
   </section>
   <section>
      <?php echo $articlesHTML; ?>
   </section>
   <footer>
      <p><?php echo $pageInfo['copyright'];?><a href="http://truger.net">Truger.net</a>.</p>
   </footer>
</body>
</html>

