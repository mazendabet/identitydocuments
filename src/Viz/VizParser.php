<?php

namespace werk365\IdentityDocuments\Viz;

class VizParser extends Viz
{
    private array $viz = [];

    public function match($parsed, $mrz, $text){
        $ignore = "\n*\s*";
        $mrzCharacters = str_split($mrz);
        foreach($mrzCharacters as $key => $character){
            $mrzCharacters[$key] = $character . $ignore;
        }
        $mrzRegex = implode($mrzCharacters);
        $text = preg_replace("/$mrzRegex/", '', $text);
        $text = preg_replace("/\n/", ' ', $text);

        $words = explode(' ', $text);
        $this->viz['first_name'] = [];
        foreach($words as $word){
            if($this->compare($parsed['last_name'], $word)){
                $this->viz['last_name'] = $word;
            }
            foreach($parsed['first_name'] as $key => $first_name){
                if($this->compare($parsed['first_name'][$key], $word)){
                    $this->viz['first_name'][$key] = $word;
                }
            }
        }
        return $this->viz;
    }

    private function compare($mrz, $viz){
        $viz = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $viz);
        $viz = preg_replace('/([ ]|[-])/', '<', $viz);
        $viz = preg_replace("/\p{P}/u", '', $viz);
        if($this->fuzzy_match($mrz, $viz, 0)['match']){
            return true;
        }
        return false;
    }

   private function fuzzy_match($query,$target,$distance) {
        ##  set max substitution steps if set to 0
        if ($distance == 0) {
            $length = strlen($query);
            if ($length > 10) {
                $distance = 4;
            }
            elseif ($length > 6) {
                $distance = 3;
            }
            else {
                $distance = 2;
            }
        }
        $lev = levenshtein(strtolower($query), strtolower($target));
        if ($lev <= $distance) {
            return array('match' => 1, 'distance' => $lev, 'max_distance' => $distance);
        }
        else {
            return array('match' => 0, 'distance' => $lev, 'max_distance' => $distance);
        }
    }
}
