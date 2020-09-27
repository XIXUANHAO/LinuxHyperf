<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace App\Controller;

use Hyperf\DbConnection\Db;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Utils\Coroutine;
use Swoole\Coroutine\Channel;

/**
 * @AutoController(prefix="user")
 */
class IndexController extends AbstractController
{
    public function index(RequestInterface $request, ResponseInterface $response)
    {
        $user = $request->input('user', 'Hyperf');
        $method = $request->getMethod();

        $data = [
            'method'    => $method,
            'all'       => $request -> all(),
            'url'       => $request -> url(),
            'query'     => $request -> query(),
            'Cookies'   => $request -> getCookieParams(),

        ];
        $int = intval(1);
        co(function (){
            $channel = new Channel();

            go(function () use ($channel) {
                $channel->push('data');
            });

            $int = Coroutine::id();
            $data = new IndexController();
            $data ->index();
            var_dump($int);
            var_dump($channel->pop().'aaaa');
            Db::table('auv_miniapp_user')
                -> where('user', $data)
                -> first();
//            var_dump($data);
        });
       return $response -> xml($data);

    }
}
