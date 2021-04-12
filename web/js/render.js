function renderMapImage(img, x, y) {
    var canvas = $("#levelCanvas")[0];
    var ctx = canvas.getContext("2d");
    ctx.drawImage(img, x * 50, y * 50, 50, 50);
}
function clearCanvas() {
    var canvas = $("#levelCanvas")[0];
    var ctx = canvas.getContext("2d");
    ctx.clearRect(0, 0, canvas.width, canvas.height);
}
function renderPlayer(x, y) {
    var img = $("#player-icon")[0];
    renderMapImage(img, x, y);
}

function renderMonster(x, y) {
    var img = $("#monster-icon")[0];
    renderMapImage(img, x, y);
}
function renderPotion(x, y) {
    var img = $("#postion-icon")[0];
    renderMapImage(img, x, y);
}

function renderBoss(x, y) {
    var img = $("#boss-icon")[0];
    renderMapImage(img, x, y);
}

function renderMap() {
    var map = store.map;
    // an additional feature would be to render static objects like walls, decorations, obstacles etc.
    // however, another way to do this is to render even static objects as items that cant be collected
    // that might allow for a destructible environment.
    renderMapItems();
}

function renderMapItems() {
    for (var i = 0; i < store.mapItems.length; i++) {
        var item = store.mapItems[i];
        renderItem(item.itemID, item.x, item.y);
    }
}

function renderItem(id, x, y) {
    // ID allows for multiple types of items
    // we would probably also want some sort of JSON or DB map 
    // that links an item with the graphics/sound resources
    var img = getImageForItem(id);
    renderMapImage(img, x, y);
}

function getImageForItem(id) {
    switch(id) {
        case 1:  // attack potion
        return img = $("#red-potion-icon")[0];
        break;
        case 2:  // defense potion
        return img = $("#blue-potion-icon")[0];
        break;
        case 3:  // bomb potion
        return img = $("#bomb-icon")[0];
        break;
    }
}

function renderInventory() {
    clearInventory();
    var limit = Math.min(store.playerItems.length, 5);
    for(var i = 1; i<= limit; i++ ){
        var id = store.playerItems[i-1].itemID;
        var item = selectItem(id);
        renderInventoryItem(i, item,  store.playerItems[i-1].qty);
    }
}

function clearInventory() {
    for(var i = 1; i<= 5; i++) {
        $("#inv"+i).attr("src", $("#blank-icon")[0].src);
        $("#inv"+i+"desc").html("");
    }
}

function renderInventoryItem(itemNo, item, qty) {
    var qtyDesc = qty> 1? ' ' +qty: '';
    var img = getImageForItem(item.id);
    $("#inv"+itemNo).attr("src", img.src);
    $("#inv"+itemNo+"desc").html(item.name + qtyDesc);
    
}


function renderEnemies() {
    for (var i = 0; i < store.activeEnemies.length; i++) {
        var enemy = store.activeEnemies[i];
        renderMonster(enemy.x, enemy.y);
    }
}


function playerMessage(message) {
    console.log(message);
    $("#message").html(message);
    
}
function clearMessage() {
    $("#message").html('');
}

function renderHUD() {
    var p = store.player;
    $("#hud").html(p.name+ " ATK:" + p.atk+ "  DEF:" + p.def+ "  HP:" + p.hp+ "  XP:" + p.xp);
    
}

function render() {
    clearCanvas();
    renderMap();
    renderPlayer(store.playerLoc.x, store.playerLoc.y);
    renderEnemies();
    renderHUD();
    renderInventory();
}
