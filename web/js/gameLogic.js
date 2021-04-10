function checkCombat() {
    if (store.player.atk <=1) {
        playerMessage("You can't attack.  You're too weak.  Find your strength!");
    }
}

function isMonster(loc) {
    return store.enemies.findIndex((enemy) => (enemy.x == loc.x) && (enemy.y == loc.y)) > -1;
}

function getMonsterAt(loc) {
    return store.enemies.find((enemy) => (enemy.x == loc.x) && (enemy.y == loc.y));
}

function isItem(loc) {
    var index = store.mapItems.findIndex((item) => (item.x == loc.x) && (item.y == loc.y));
    return index > -1;
}

function selectItem(id) {
    return store.items.find((item) => item.id == id);
}

function getItemAt(loc) {
    var item = store.mapItems.find((item) => (item.x == loc.x) && (item.y == loc.y));
    return selectItem(item.itemID);
}

function isEdge(location) {
    if ((location.x < 0) || (location.y < 0) || (location.x > store.map.width-1) || (location.y > store.map.height-1)) {
        return true;
    }
    return false;
}


function movePlayerDelta(x, y) {

    clearMessage();
    var newLoc = { x: store.playerLoc.x + x, y: store.playerLoc.y + y };
    if (isEdge(newLoc)) {
        return;
    }
    if (isMonster(newLoc)) {
        checkCombat()
        return;
    }

    if (isItem(newLoc)) {
        checkItem(newLoc);
        return;
    }

    store.playerLoc.x = newLoc.x;
    store.playerLoc.y = newLoc.y;
    render();

}

function checkItem(loc) {
    var item = getItemAt(loc);
    var promise = addPlayerItem(item, store.player.id);

        // isn't real until the server says it's real
        $.when(promise).done(() => {
            playerMessage("You found an "+ item.name);
            removeItemFromMap(loc);
            render();
        });
  
    
}

function useItem(slot) {
    if (store.playerItems.length  < slot){
        console.log("no item for slot " + slot);
        return;
    }
    var playerItem = store.playerItems[slot -1];
    var item = selectItem(playerItem.itemID);
    if (item.playerEffect) {
        var clonePlayer = {...store.player};
        var effectKeys = Object.keys(item.playerEffect);
        if (effectKeys) {
            effectKeys.forEach( (key) => { clonePlayer[key]+= item.playerEffect[key];
                // of course in here would be neat to put in all kinds of effects like 
                // saying the player was stronger, or weaker etc. 
                playerMessage("You drank the potion!  " + key + " +" + item.playerEffect[key]);
            
            });
        }
        var cloneItems = [...store.playerItems];
        cloneItems.splice(slot-1, 1);
        var persist = persistGameData({player:clonePlayer, playerItems:cloneItems});
        $.when(persist).done(()=> {
            render();
        });
    }

 
}

function addPlayerItem(item, playerID) {
    var cloneItems = [ ...store.playerItems ];
    var existing = cloneItems.find((cloneItem) => cloneItem.itemID == item.id);
    if (existing) {
        existing.qty ++;
    } else {
        var playerItemObject = {playerID: playerID, itemID: item.id, qty: 1};
        cloneItems.push(playerItemObject);
    }
    return persistPlayerItems(cloneItems, store.player.id);



}

function removeItemFromMap(loc) {
    var index = store.mapItems.findIndex((item) => (item.x == loc.x) && (item.y == loc.y));
    store.mapItems.splice(index, 1);
}

function movePlayer(x, y) {
    store.playerLoc.x = x;
    store.playerLoc.y = y;
    render();
}


    // only update if the persist was successful
    // don't want to have a player that thinks they have an item
    // when the backend doesn't agree
function persistPlayerItems(playerItems, playerID) {

    return $.post("api/playerItemPOST.php", { playerItems, playerID }, function (result) {
        var dbPlayerItems = JSON.parse(result);
        store.playerItems = dbPlayerItems;
       
    });
}

function persistGameData(data) {

    if (data.playerItems && data.playerItems.length == 0){
        data.playerItems = "empty";
    }
    data.playerID = store.player.id;
    return $.post("api/gameData.php", data , function (result) {
        var dbData = JSON.parse(result);
        store = { ...store, ...dbData };
        if (store.playerItems === 'empty') {
            store.playerItems = [];
        }
    });
}

function persistPlayer(player) {
    return $.post("api/playerPOST.php", player, function (result) {
        var dbPlayer = JSON.parse(result);
        console.log('dbplayer', dbPlayer);
        store.player = dbPlayer;
        renderHUD();
    });
}

function isAvailable(location) {
    // player
    if (location.x == store.playerLoc.x && location.y == store.player.y) {
        return false;
    }
    // enemy 
    if (isMonster(location)){
        return false;
    }
    // item
    if (isItem(location)) {
        return false;
    }
    return true;
}

function spawnEnemy(x, y) {
    var enemy = { x: x, y: y, hp: 5 };
    store.enemies.push(enemy);
}
