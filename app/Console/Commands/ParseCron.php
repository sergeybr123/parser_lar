<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Article;
use App\Models\ArticleLog;
use Carbon\Carbon;

class ParseCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://static.feed.rbc.ru/rbc/logical/footer/news.rss");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
        $xmlstr = curl_exec($ch);
        curl_close($ch);

        $xmlobj = new \SimpleXMLElement($xmlstr);
        $results = $xmlobj->xpath('channel/item');

        foreach($results as $result) {
            $article = Article::updateOrCreate(
                ["name" => $result->title],
                ["name" => $result->title,
                    "link" => $result->link,
                    "description" => $result->description,
                    "date_created" => Carbon::parse($result->pubDate)->format("Y-m-d H:m:s"),
                    "author" => $result->author]
            );
            $article->save();

            $log = ArticleLog::create([
                "date_created" => Carbon::parse($result->pubDate)->format("Y-m-d H:m:s"),
                "method" => "curl",
                "code" => "code",
                "body" => $result->description
            ]);
            $log->save();

            return 0;
        }
    }
}
