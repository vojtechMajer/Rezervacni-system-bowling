<?php
class RouteController extends Controller
{
    public $controller; // objekt kontroleru

    // podle URL ($parametry[0]) "předané z indexu" 
    // nalezne správný kontroler a předá mu řízení 
    public function load($parameters)
    {
        $url = $parameters[0];

        $castiCesty = $this->parseURL($url);

        if (empty($castiCesty[0])) {
            // přesměrujeme na výchozí kontroler
            $this->redirect("index");
        } else {
            // v URL je neprázdná cesta (tedy určen kontroler)      
            // ["uzivatel", "10", "editace"]    // $castiCesty
            $castNazvuKontroleru = array_shift($castiCesty); // vrátí první prvek a ostatní prvky posune na začátek
            $nazevKontroleru = $this->kebabToPascalCase($castNazvuKontroleru) . "Controller";

            if (file_exists("controllers/$nazevKontroleru.php")) {
                $this->controller = new $nazevKontroleru;
                $this->controller->load($castiCesty);

                $this->view = "layout";
            } else {
                // třída kontroleru neexistuje
                // přesměrujeme na chybový kontroler
                $this->redirect("error");
            }

            $this->data["messages"] = $this->getMessages();
        }
    }

    // z editace-studenta udělá EditaceStudenta
    private function kebabToPascalCase($text)
    {
        $text = str_replace("-", " ", $text);
        $text = ucwords($text);
        $text = str_replace(" ", "", $text);
        return $text;
    }

    // z https://localhost/uzivatel/10/editace
    // udělá pole
    // ["uzivatel", "10", "editace"]
    private function parseURL($url)
    {
        $naparsovanaURL = parse_url($url);
        $cesta = $naparsovanaURL["path"];

        $cesta = ltrim($cesta, "/"); // odebere počáteční lomítko
        $cesta = trim($cesta); // odebere bílé znaky

        $rozdelenaCesta = explode("/", $cesta);

        return $rozdelenaCesta;
    }
}
