<?php

namespace App\Traits\Providers;
use App\Models\GamesKey;
use App\Models\Order;
use App\Models\User;
use App\Traits\Missions\MissionTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request as FacadesRequest;
use App\Helpers\Core as Helper;

trait QualityGamesTrait
{
    use MissionTrait;

    /**
     * @dev 𝓗𝓐𝓡𝓚𝓩𝓘𝓜 / by OndaGames.com < - Esse sistema e Gratuito - Entre no nosso Grupo  https://t.me/+dFr8-1AmUz5hZDc5
     * @var string
     */
    protected static $urlQuality;
    protected static $keyQuality;
    protected static $hallQuality;

    private static function credencialQuality(){
        $setting = GamesKey::first();
        self::$urlQuality          = $setting->getAttributes()['quality_url'];
        self::$keyQuality          = $setting->getAttributes()['quality_key'];
        self::$hallQuality         = $setting->getAttributes()['quality_hall'];
    }
    /**
     * @dev 𝓗𝓐𝓡𝓚𝓩𝓘𝓜 / by OndaGames.com < - Esse sistema e Gratuito - Entre no nosso Grupo  https://t.me/+dFr8-1AmUz5hZDc5
     * @return void
     */
   
    public static function webhookQuality(Request $request){
        $tipo = $request->input("cmd");
        Log::info($request->all());
        switch($tipo){
            case 'getBalance':
                return self::getBalanceQuality($request);
            case 'writeBet':
                return self::writeBetQuality($request);
        }
    }
    public static function qualityLaunch($id, $demo){
        self::credencialQuality();
        $dominio = FacadesRequest::getSchemeAndHttpHost();
        $postArray = [
            "cmd" => "openGame",
            "hall" => self::$hallQuality, 
            "domain" => $dominio, 
            "exitUrl" => $dominio. '?target=_top', 
            "language" => "pt", 
            "continent" => "BRL", 
            "key" => self::$keyQuality, 
            "login" => Auth::guard("api")->user()->email, 
            "gameId" => $id, 
            "cdnUrl" => "", 
            "demo" => $demo 

        ];
        $response = Http::post(self::$urlQuality . "/openGame/", $postArray);
        if($response->successful()) {
            $data = $response->json();
            return ["launch_url" => $data['content']['game']['url']];
        }

    }
    private static function getBalanceQuality($dados){
        $user = User::where("email", $dados->input("login"))->first();
        if($user != null){
            $saldo = (float)$user->wallet->balance + (float)$user->wallet->balance_bonus + (float) + (float)$user->wallet->balance_withdrawal;
            return response()->json(["status" => "success", "error" => "", "login" => $user->email, "balance" => number_format($saldo, 2, ".", "."), "currency" => "BRL"]);
        }else{
            return response()->json(["status" => "fail", "error" => "user_not_found"]);
        }
    }
    private static function writeBetQuality($dados){
        $user = User::where("email", $dados->input("login"))->first();
        if($user != null){
            $wallet = $user->wallet;
            $bet = $dados->input("bet");
            $win = $dados->input("win");
            $saldoAnt = (float)$wallet->balance + (float)$wallet->balance_bonus + (float)$wallet->balance_withdrawal;
            $saldo = ((float)$wallet->balance + (float)$wallet->balance_bonus + (float)$wallet->balance_withdrawal) - $bet + $win;
            $id = rand(0, 9999999999);
            $changeBonus = null;
            if($saldoAnt >= $bet){
                if($wallet->balance_bonus > $bet) {
                    $wallet->decrement('balance_bonus', $bet); /// retira do bonus
                    $changeBonus = 'balance_bonus'; /// define o tipo de transação
                }elseif($wallet->balance >= $bet) {
                    $wallet->decrement('balance', $bet); /// retira do saldo depositado
                    $changeBonus = 'balance'; /// define o tipo de transação
                }elseif($wallet->balance_withdrawal >= $bet) {
                    $wallet->decrement('balance_withdrawal', $bet); /// retira do saldo liberado pra saque
                    $changeBonus = 'balance_withdrawal'; /// define o tipo de transação
                }
                if($bet == 0 && $win != 0){
                    $changeBonus = "balance";

                }
                if($bet != 0 || $win != 0){
                    Order::create([
                        "user_id" => $user->id,
                        "session_id" => $dados->input("sessionId"),
                        "transaction_id" => $dados->input("tradeId"),
                        "game" => $dados->input("gameId"),
                        "game_uuid" => $dados->input("gameId"),
                        "type" => $bet == 0 ? "win" : "bet",
                        "type_money" => $changeBonus,
                        "amount" =>  $bet == 0 ? $win : $bet,
                        "providers" => "quality_games",
                        "refunded" => false,
                        "round_id" => $dados->input("sessionId"),
                        "status" => true
                    ]);
                    Helper::generateGameHistory($user->id, $bet == 0 ? "win" : "loss", $win, $bet, $changeBonus, $dados->input("tradeId"));
                }
                return response()->json(["status" => "success", "error" => "", "login" => $user->email, "balance" => number_format($saldo, 2, ".", "."), "currency" => "BRL", "operationId" => $id]);

            }else{
                return response()->json(["status" => "fail", "error" => "fail_balance"]);

            }
        }else{
            return response()->json(["status" => "fail", "error" => "user_not_found"]);

        }
    } 

}


?>
