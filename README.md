# Synapse Demo - Chuck Thomas for Synapse Interview

To fulfill the requirements of the interview test, I created this game. While the original requirements implied a 100% PHP server rendered game, I decided to make it a little more realistic and make a full 3-tier game with vanilla JS (with JQuery) on the web client thus more realistically modelling how a PHP API and RDBMS would be used with a client such as Unity.

You can play it here: (Syapse Rogue)[https://synapse-rogue.herokuapp.com/]


## Playing
The play is simple.  At the start of the game you will see a map which has the player.  At the top are the player stats.
The map is randomly populated by 3 enemies.  The player is also given 2 potions.  The enemy placement is random, but the potion locations are static and are in the DB.
The game is over if the player HP is below 0.  The game will then reset.
The game is won if all demons are defeated.  The game will then reset.

## Moving
Move the player (the ninja icon) by using the arrow keys. 

## Gaining and Using Objects
To increase the attack, move up to the potion bottle. One is an attack potion and the other is a defense.  By moving into their space you will add them to your inventory shown on the right.
To use the potion, simply use the number key that corresponds with it's inventory slot.
Another way to gain an object is to defeat an enemy.  It will reward you with a random item.

There are currently 3 items:
Attack Potion - raise player atk by 1
Defense Potion - raise player def by 1
Bomb - reduce the HP of all enemies on the map by 10.

## Battle
If the game has been reset, the player will only have 1 attack and 1 defense.  The game will not allow you to attack a demon unless you have at least 1 attack.
To attack, simply move into the a demon.
When you move into a demon damage will be calculated by simulating rolling one die per attack value.  This becomes your damage to the enemy.  However, the enemy's defense is then subtracted from that damage value.  The enemy HP is reduced by the remaining damage. 
If the enemy survives your initial attack, it will counterattack in the same way.  It rolls some dice, applies damage which is reduced by your DEF attribute.
If the enemy does not survive, it is removed from the map and the player is rewarded with a random item as well as some XP.

## Extra Features
Extra features not in the requirements might be:

-Items that are added to your inventory which already contain the same item will increase a QTY value.  Using an item with a QTY > 1 will not remove it but reduce the QTY value. (required both client and back-end implementation)
-Bomb effect (mostly just client side)
-By the DB and API schema, could support multiple maps and multiple players though right now it only has one of each.
-Reset button which executes a DB sproc to reset the database and reload the game. (really handy for testing)
-Items have an effect field which is a JSON set of attributes describing how the use of that item will affect the player or enemies.  This allows easy expansion of possibilities for item uses without having to modify the database.


## Architecture
When the player is moving around the map, nothing is being sent to the server, thus, if the player only moves, and then reloads the game, they will start back at the start position for the map.

The client is responsible for rendering all HTML as well as handling player input and movement.  It also handles most game logic with the exception of rewards (below).

The PHP layer is responsible for serving data to the client as well as handling data updates in the form of POST calls.  It also handles reward logic so that none of that is on the client.  More could be handled in PHP but that would significantly slow down the game experience.  The PHP layer also only calls stored procedures on the DB.  It does not construct SQL syntax beyond sproc calls.  This helps to encapsulate the SQL (a concern of the DB server) and also provides added security as the DB might check to see if the caller has access privileges to the requested data or update.

The DB layer of course is a normalized set of tables that handle persisted data.  SQL statements do not exist in the PHP layer

Persisted data includes:
- Any change to player stats, xp, hp, atk, def.
- Any change to the players inventory.

Thus, if the player gains xp, loses hp or gains items, if they reload the browser, the map will be as it was at the start, but the player will retain their same stats and inventory.  This would allow the game to easily be expanded to include more maps as well as keep the player from becoming frustrated if they lost an internet connection.  All of their gains would be maintained.

Another architecture point is that no persisted changes in the game state are reflected at all in the UI until they are confirmed by the back end.  This is so that in the event of an error or connection loss, the player would not perceive losing something they thought they had earned.

Server Game Logic
- Battle Rewards - When the player wins a battle, the rewards are handled entirely in the REST API

## Database
The database consists of several tables as well as stored procedures.  The schema is fully normalized:

Tables
enemy - possible enemies in the game which also contains items that each enemy can drop.
item - possible items in the game.
map - data for the UI to construct the map
mapitems - items that will be placed on the map at startup (not persisted with in-game changes, that would require a map-item-player table).
player - data for the player
playeritems - the player's item inventory

Stored Procedures
clearPlayerItems - erase all items for a player.
gameDataSetup - erases tables and inserts initial data.  This is called by clicking the "reset game" button.
getAllEnemies - all enemies from the enemy table
getAllItems - all items from the item table
getEnemy - get a specific enemy by ID
getMap - get the game map (can have ID as attribute though now it is only 1)
getMapItems - get the items for a given map ID.
getPlayer - get the game player (expansion would allow multiple players that could log in)
getPlayerItems - a player's inventory
updatePlayer - update a player's statistics
upsertPlayerItem - insert or update an item in the player's inventory.  If it already exists, it's QTY is increased.

You may notice that some sprocs such as deleting items are not included.  This is because I have found that with small data sets, the simplicity of logic involved in deleting everything and re-inserting (such as player items) outweighs small inefficiency.  I would of course install proper CRUD sprocs for data sets that are much larger.

## PHP
The PHP files consist mainly of classes that handle the DB interactions for given entities as well as route endpoint handlers for GET and POST.
An exception to this is :
setup.php - calls the sproc to reset the game database.
rewardPlayerPOST.php - determines player rewards.

Also, while some entities can be retrieved and updated via endpoints specific to those entities, I have found it much more efficient to make endpoints that send and receive udpates to multiple entities at once if it is known that they could be needed at the same time.  This minimizes API calls from the client.

gameData.PHP returns player, map, mapItems, enemies, playerItems and items.  All at once.
It can also update both a player and playerItems in the same call.

rewardPlayerPOST.php also updates both player and playerItems in the same call.

Some people minimize API calls by nesting data in JSON such as each player having a playerItems attribute.  However, I have found through experience that keeping data as flat/normalized entities makes REST API logic much simpler, especially for CRUD operations.


## Client
The client is minimal and is vanilla JS with $jQuery.  Since it is my understanding that the front end will not be evaluated as much as the PHP and Database, I won't spend as much time documenting the client here.
I will note that as it is a "quick and dirty" front end, it doesn't handle things like modules which I would certainly use if I were building a production app.


## Future Improvement
-If I were going to expand this further I would add:
-Multiple players.
-Multiple maps.
-A table of enemy/item drop rates that can be adjusted by game designers.

