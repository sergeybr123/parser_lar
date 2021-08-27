<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\ArticleLog;
use Carbon\Carbon;

class ArticleController extends Controller
{
    public function parse(){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://static.feed.rbc.ru/rbc/logical/footer/news.rss");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
        $xmlstr = curl_exec($ch);
        curl_close($ch);

        $xmlobj = new \SimpleXMLElement($xmlstr);
        $results = $xmlobj->xpath('channel/item');

        foreach($results as $result) {
            $article = Article::firstOrCreate(
                ["name" => $result->title],
                ["name" => $result->title,
                "link" => $result->link,
                "description" => substr($result->description, 0, 500),
                "date_created" => Carbon::parse($result->pubDate)->format("Y-m-d H:m:s"),
                "author" => $result->author]
            );
            $article->save();
            $this->articleLog($result);
        }
    }


    public function articleLog($item) {
//        dd($item->pubDate);
        $log = ArticleLog::create([
            "date_created" => Carbon::parse($item->pubDate)->format("Y-m-d H:m:s"),
            "method" => "curl",
            "code" => "code",
            "body" => substr($item, 0, 255)
        ]);
        $log->save();
    }
}
