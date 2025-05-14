<?php

namespace App\Http\Controllers\Api\Games;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CategoryGame;
use App\Models\Game;
use App\Models\GameFavorite;
use App\Models\GameLike;
use App\Models\GamesKey;
use App\Models\Gateway;
use App\Models\Provider;
use App\Models\Wallet;
use App\Traits\Providers\FiversTrait;
use App\Traits\Providers\Games2ApiTrait;
use App\Traits\Providers\KaGamingTrait;
use App\Traits\Providers\QualityGamesTrait;
use App\Traits\Providers\PlayFiverTrait;

use App\Traits\Providers\SalsaGamesTrait;
use App\Traits\Providers\VibraTrait;
use App\Traits\Providers\WorldSlotTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GameController extends Controller
{
    use KaGamingTrait, FiversTrait, QualityGamesTrait, PlayFiverTrait, VibraTrait, SalsaGamesTrait, WorldSlotTrait, Games2ApiTrait;

    /**
     * @dev 𝓗𝓐𝓡𝓚𝓩𝓘𝓜 / by OndaGames.com < - Esse sistema e Gratuito - Entre no nosso Grupo  https://t.me/+dFr8-1AmUz5hZDc5
     * Display a listing of the resource.
     */
    public function index()
    {
        $providers = Provider::with(['games', 'games.provider'])
            ->whereHas('games')
            ->orderBy('name', 'desc')
            ->where('status', 1)
            ->get();

        return response()->json(['providers' =>$providers]);
    }
    public function gamesCategories()
    {
        $categories = Category::all();
        $games = [];
    
        foreach ($categories as $category) {
            $categoryGames = CategoryGame::where("category_id", $category->id)->get();
    
            if ($categoryGames->isNotEmpty()) {
                $numIterations = min(12, $categoryGames->count());
    
                for ($i = 0; $i < $numIterations; $i++) {
                    $game = Game::where("id", $categoryGames[$i]->game_id)->where("status", 1)->first();
    
                    if ($game != null) {
                       
                        $games[$category->name]['games'][$game->game_name] = $game;
                    }
                }
                
                $games[$category->name]["quantidade"] = CategoryGame::where("category_id", $category->id)->whereHas("game",function (Builder $query) {
                    $query->where("status",1);
                })->count();
                $games[$category->name]["pagina"] = 1;
                if($games[$category->name]["quantidade"] <= 12){
                    $games[$category->name]["UPagina"] = 1;
                }
                $games[$category->name]["quantidadeA"] = count($games[$category->name]['games']);

            }
        }
    
        return response()->json(['games' => $games]);
    }
    /**
     * @dev 𝓗𝓐𝓡𝓚𝓩𝓘𝓜 / by OndaGames.com < - Esse sistema e Gratuito - Entre no nosso Grupo  https://t.me/+dFr8-1AmUz5hZDc5
     * @return \Illuminate\Http\JsonResponse
     */
    public function featured()
    {
        $featured_games = Game::with(['provider'])->where('is_featured', 1)->get();
        return response()->json(['featured_games' => $featured_games]);
    }

    /**
     * Source Provider
     *
     * @dev 𝓗𝓐𝓡𝓚𝓩𝓘𝓜 / by OndaGames.com < - Esse sistema e Gratuito - Entre no nosso Grupo  https://t.me/+dFr8-1AmUz5hZDc5
     * @param Request $request
     * @param $token
     * @param $action
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function sourceProvider(Request $request, $token, $action)
    {
        $tokenOpen = \Helper::DecToken($token);
        $validEndpoints = ['session', 'icons', 'spin', 'freenum'];

        if (in_array($action, $validEndpoints)) {
            if(isset($tokenOpen['status']) && $tokenOpen['status'])
            {
                $game = Game::whereStatus(1)->where('game_code', $tokenOpen['game'])->first();
                if(!empty($game)) {
                    $controller = \Helper::createController($game->game_code);

                    switch ($action) {
                        case 'session':
                            return $controller->session($token);
                        case 'spin':
                            return $controller->spin($request, $token);
                        case 'freenum':
                            return $controller->freenum($request, $token);
                        case 'icons':
                            return $controller->icons();
                    }
                }
            }
        } else {
            return response()->json([], 500);
        }
    }

    /**
     * @dev 𝓗𝓐𝓡𝓚𝓩𝓘𝓜 / by OndaGames.com < - Esse sistema e Gratuito - Entre no nosso Grupo  https://t.me/+dFr8-1AmUz5hZDc5
     * Store a newly created resource in storage.
     */
    public function toggleFavorite($id)
    {
        if(auth('api')->check()) {
            $checkExist = GameFavorite::where('user_id', auth('api')->id())->where('game_id', $id)->first();
            if(!empty($checkExist)) {
                if($checkExist->delete()) {
                    return response()->json(['status' => true, 'message' => 'Removido com sucesso']);
                }
            }else{
                $gameFavoriteCreate = GameFavorite::create([
                    'user_id' => auth('api')->id(),
                    'game_id' => $id
                ]);

                if($gameFavoriteCreate) {
                    return response()->json(['status' => true, 'message' => 'Criado com sucesso']);
                }
            }
        }
    }

    /**
     * @dev 𝓗𝓐𝓡𝓚𝓩𝓘𝓜 / by OndaGames.com < - Esse sistema e Gratuito - Entre no nosso Grupo  https://t.me/+dFr8-1AmUz5hZDc5
     * Store a newly created resource in storage.
     */
    public function toggleLike($id)
    {
        if(auth('api')->check()) {
            $checkExist = GameLike::where('user_id', auth('api')->id())->where('game_id', $id)->first();
            if(!empty($checkExist)) {
                if($checkExist->delete()) {
                    return response()->json(['status' => true, 'message' => 'Removido com sucesso']);
                }
            }else{
                $gameLikeCreate = GameLike::create([
                    'user_id' => auth('api')->id(),
                    'game_id' => $id
                ]);

                if($gameLikeCreate) {
                    return response()->json(['status' => true, 'message' => 'Criado com sucesso']);
                }
            }
        }
    }

    /**
     * @dev 𝓗𝓐𝓡𝓚𝓩𝓘𝓜 / by OndaGames.com < - Esse sistema e Gratuito - Entre no nosso Grupo  https://t.me/+dFr8-1AmUz5hZDc5
     * Display the specified resource.
     */
    public function show(string $id)
    {
       
        $game = Game::with(['categories', 'provider'])->whereStatus(1)->find($id);
        if(!empty($game)) {

            if(Auth::guard("api")->check()) {

                $wallet = Wallet::where('user_id', auth('api')->id())->first();
                if($wallet->total_balance > 0) {
                    $game->increment('views');

                    $token = \Helper::MakeToken([
                        'id' => auth('api')->id(),
                        'game' => $game->game_code
                    ]);

                    switch ($game->distribution) {
                        case 'source':
                            return response()->json([
                                'game' => $game,
                                'gameUrl' => url('/originals/'.$game->game_code.'/index.html?token='.$token),
                                'token' => $token
                            ]);
                        case 'salsa':
                            return response()->json([
                                'game' => $game,
                                'gameUrl' => self::playGameSalsa('CHARGED', 'BRL', 'BR', $game->game_id),
                                'token' => $token
                            ]);
                        case 'kagaming':
                            return response()->json([
                                'game' => $game,
                                'gameUrl' => self::GameLaunchKaGaming($game->game_server_url, $game->game_code),
                                'token' => $token
                            ]);
                        case 'vibra_gaming':
                            return response()->json([
                                'game' => $game,
                                'gameUrl' => self::GenerateGameLaunch($game),
                                'token' => $token
                            ]);
                        case 'fivers':
                            $fiversLaunch = self::GameLaunchFivers($game->provider->code, $game->game_id, 'pt', auth('api')->id());

                            if(isset($fiversLaunch['launch_url'])) {
                                return response()->json([
                                    'game' => $game,
                                    'gameUrl' => $fiversLaunch['launch_url'],
                                    'token' => $token
                                ]);
                            }

                            return response()->json(['error' => $fiversLaunch, 'status' => false ], 400);
                        case 'quality_games':
                            $qualityLaunch = self::qualityLaunch($game->game_id, $game->only_demo);
                            return response()->json([
                                'game' => $game,
                                'gameUrl' => $qualityLaunch['launch_url'],
                                'token' => $token
                            ]);
                        
                        case 'play_fiver':
                            $playfiver = self::playFiverLaunch($game->game_id, $game->only_demo);
                            return response()->json([
                                'game' => $game,
                                'gameUrl' => $playfiver['launch_url'],
                                'token' => $token
                            ]);
                        case 'games2_api':
                            $games2ApiLaunch = self::GameLaunchGames2($game->provider->code, $game->game_id, 'pt', auth('api')->id());

                            if(isset($games2ApiLaunch['launch_url'])) {
                                return response()->json([
                                    'game' => $game,
                                    'gameUrl' => $games2ApiLaunch['launch_url'],
                                    'token' => $token
                                ]);
                            }

                            return response()->json(['error' => $games2ApiLaunch, 'status' => false ], 400);
                        case 'worldslot':
                            $worldslotLaunch = self::GameLaunchWorldSlot($game->provider->code, $game->game_id, 'pt', auth('api')->id());

                            if(isset($worldslotLaunch['launch_url'])) {
                                return response()->json([
                                    'game' => $game,
                                    'gameUrl' => $worldslotLaunch['launch_url'],
                                    'token' => $token
                                ]);
                            }

                            return response()->json(['error' => $worldslotLaunch, 'status' => false ], 400);

                    }
                }
                return response()->json(['error' => 'Você precisa ter saldo para jogar', 'status' => false, 'action' => 'deposit' ], 200);
            }else{
                return response()->json(['error' => 'Você precisa tá autenticado para jogar', 'status' => false ], 400);

            }
        }
        return response()->json(['error' => '', 'status' => false ], 500);
    }

    /**
     * @dev 𝓗𝓐𝓡𝓚𝓩𝓘𝓜 / by OndaGames.com < - Esse sistema e Gratuito - Entre no nosso Grupo  https://t.me/+dFr8-1AmUz5hZDc5
     * Show the form for editing the specified resource.
     */
    public function allGames(Request $request)
    {
        $query = Game::query();
        $query->with(['provider', 'categories']);

        if (!empty($request->provider) && $request->provider != 'all') {
            $query->where('provider_id', $request->provider);
        }

        if (!empty($request->category) && $request->category != 'all') {
            $query->whereHas('categories', function ($categoryQuery) use ($request) {
                $categoryQuery->where('slug', $request->category);
            });
        }

        if (isset($request->searchTerm) && !empty($request->searchTerm) && strlen($request->searchTerm) > 2) {
            $query->whereLike(['game_code', 'game_name', 'description', 'distribution', 'provider.name'], $request->searchTerm);
        }else{
            $query->orderBy('views', 'desc');
        }

        $games = $query
            ->where('status', 1)
            ->paginate(12)->appends(request()->query());

        return response()->json(['games' => $games]);
    }

    /**
     * @dev 𝓗𝓐𝓡𝓚𝓩𝓘𝓜 / by OndaGames.com < - Esse sistema e Gratuito - Entre no nosso Grupo  https://t.me/+dFr8-1AmUz5hZDc5
     * Update the specified resource in storage.
     */
    public function webhookGoldApiMethod(Request $request)
    {
        return self::WebhooksFivers($request);
    }
    /**
     * @dev isaacroque5
     * Integrando o webhook da Quality Games.
     */
    public function webhookPlayFiver(Request $request)
    {
        return self::webhookPlayFiverAPI($request);
    }
    /**
     * @dev 𝓗𝓐𝓡𝓚𝓩𝓘𝓜 / by OndaGames.com < - Esse sistema e Gratuito - Entre no nosso Grupo  https://t.me/+dFr8-1AmUz5hZDc5
     * Update the specified resource in storage.
     */
    public function webhookUserBalanceMethod(Request $request)
    {
        return self::GetUserBalanceWorldSlot($request);
    }

    /**
     * @dev 𝓗𝓐𝓡𝓚𝓩𝓘𝓜 / by OndaGames.com < - Esse sistema e Gratuito - Entre no nosso Grupo  https://t.me/+dFr8-1AmUz5hZDc5
     * Update the specified resource in storage.
     */
    public function webhookGameCallbackMethod(Request $request)
    {
        return self::GameCallbackWorldSlot($request);
    }

    /**
     * @dev 𝓗𝓐𝓡𝓚𝓩𝓘𝓜 / by OndaGames.com < - Esse sistema e Gratuito - Entre no nosso Grupo  https://t.me/+dFr8-1AmUz5hZDc5
     * Update the specified resource in storage.
     */
    public function webhookMoneyCallbackMethod(Request $request)
    {
       
    }


    /**
     * Webhook Vibra Method
     *
     * @param Request $request
     * @param $parameters
     * @return array|\Illuminate\Http\JsonResponse|null
     */
    public function webhookVibraMethod(Request $request, $parameters)
    {
        return self::WebhookVibra($request, $parameters);
    }

    /**
     * @param Request $request
     * @return null
     */
    public function webhookKaGamingMethod(Request $request)
    {
        return self::WebhookKaGaming($request);
    }

    /**
     * @param Request $request
     * @return null
     */
    public function webhookSalsaMethod(Request $request)
    {
        return self::webhookSalsa($request);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
