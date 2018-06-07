<?php 

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class ListController extends Controller {

  function __construct(Request $request, Response $response, $args) {
    parent::__construct($request, $response, $args);
  }

  function getList() {
    if ($this->request->isXhr()) {
      $stub_data = [
        "list_info" => [
          "created_date" => "2018 MAY 25",
          "description" => "Here are some games which I think is the best in Steam",
          "cover" => "/assets/image/hollow_knight",
          "title" => "Steam best games",
          "creator" => [
            "id" => 1,
            "username" => "raymond",
            "email" => "raymond@god.com",
            "avatar" => "/assets/image/dark_soul_ava"
          ]
        ],
        "games" => [
          [
            "name" => "DARK SOULS III",
            "date" => "2015 MAY 25",
            "company" => "From Software",
            "cover" => "/assets/image/dark_soul_hero",
            "reason" => 'After an obscure, but sensational opening CG, my journey to challenge Dark Souls 3 began. A messy tombstone, fragile enemies, and pedagogical texts all over the place—all of which are so familiar, reminds me of the first-line heavenly forest that played the role of Xinshoucun in “Black Soul 2”. As I happily collected the treasures that fell to the ground, I was secretly proud of myself. "Is this tutorial level too simple for me?" Then "YOU DIED" emerged on the screen. The first death education class I gave was a crystal lizard that was deliberately placed on the "Tutorial" and released powerful frost magic.'
          ],
          [
            "name" => "Tom Clancy's Rainbow Six Siege",
            "date" => "2015 MAY 25",
            "company" => "Ubisoft",
            "cover" => "/assets/image/rainbow_six_siege",
            "reason" => "The highlight of 'Rainbow Sixth: Siege' is 'Break through' after another, depicting what happened before and after the most exciting and exciting raid in the film. The offensive party has less than a minute before the start of the battle to search for enemy and mission targets with drones, and the defense side can strengthen defense facilities at the same time, block doors, reinforce walls, mines, and destroy them with guns. The enemy's scurrying gadgets. After this, it was the C4 that broke the door, the sledge hammer broke the window, and the flash of light burst into a mass of white light..."
          ],
          [
            "name" => "Hollow Knight",
            "date" => "2015 MAY 25",
            "company" => "Team Cherry",
            "cover" => "/assets/image/hollow_knight",
            "reason" => "'Hollow Knight' presents us with a hand-painted 'Gothic' aesthetic underground world. Although the game is a 2D horizontal version, we can feel the background changes in the game. Different scenes and backgrounds are different. The dim picture coupled with the quiet scene, all reveal the Gothic strange beauty, but it is different from the style but it is a budding person, the protagonist of the unicorn, the old knight, the timid warrior. The nephew's bank grandmother and other characters are humorous and vivid. This kind of contrast is like blowing a fresh cool breeze in the darkness. It is gentle and gives hope."
          ],
          [
            "name" => "Assassin's Creed: Origins",
            "date" => "2015 MAY 25",
            "company" => "Ubisoft",
            "cover" => "/assets/image/ac_origins",
            "reason" => "Two years later, the new Assassin's Creed finally returned to our sight. Unlike previous timepieces, which are getting closer to the modern timeline, this is a direct look at ancient Egypt, which dates back to the birth of the Brotherhood. The actor Payjek is inevitably drawn into an undercurrent political game, as well as a battle between two old forces that spans over a thousand years. This is the Assassin's Creed. : The origin of the story."
          ]
        ]
      ];

      return $this->render("json", $stub_data);
    } else {

      $replacement = "var list_id = " . $this->args["id"] . ";";
      return $this->render("html", "list.html", $replacement);
    }    
  }
}
?>