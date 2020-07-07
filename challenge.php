<html>
<body>

Number of widgets ordered <?php echo $_POST["order"]; ?><br>

<?php  
$order = $_POST["order"];
$widgetPacks = array(250, 500, 1000, 2000, 5000);
// More complicated pack sizes. Some orders to test these with: 2751, 2861, 2880, 310(this was a tricky one), 1310)
// $widgetPacks = array(160,300, 413, 715, 1000, 4500);

// Some preprocessing
array_unique($widgetPacks);
define("ARRAYLENGTH", count($widgetPacks));
rsort($widgetPacks);

echo "<br>Packs to be ordered:<br>";
if($order > 0) {
	// If the order is smaller than the smallest pack, order the smallest pack
	if($order < $widgetPacks[ARRAYLENGTH - 1]) {
		echo $widgetPacks[ARRAYLENGTH - 1] . " X 1";
	} else {
		// If the order is bigger than the biggest pack, order the biggest one until others should be ordered
		$bigOrder = array();
		while($order > $widgetPacks[0]) {
			array_push($bigOrder, $widgetPacks[0]);
			$order -= $widgetPacks[0];
		}
		
		// This is where the array that keeps track of all the boxes is made
		$smallOrder = packsToSend($order, 0, $widgetPacks);
		for ($i = 0; $i < count($smallOrder); $i++) {
			array_push($bigOrder, $smallOrder[$i]);
		}
		
		// Output algorithm
		rsort($bigOrder);
		$counter = 1;
		$i = 0;
		while ($i < count($bigOrder) - 1) {
			if ($bigOrder[$i] == $bigOrder[$i + 1]) {
				$counter++;
			} else {
				echo $bigOrder[$i] . " X " . $counter . "<br>";
				$counter = 1;
			}
			$i++;
		}
		echo $bigOrder[$i] . " X " . $counter . "<br>";
	}
} else {
	echo "Incorrect input";
}

// The recursive function which calculates the best possible way to order boxes by the rules of least extra widgets, and smallest number of boxes.
// This algorithm recursively assumes that the box that is smaller than the order will be bought, until the end, where it chooses to either buy 2 
// of the smallest boxes, or one bigger box. After the recursion calls, while going back down the stack, the algorithm checks if it was worth buying
// more small boxes, than one of bigger size.
function packsToSend ($order, $iterator, $widgetPacks) {
	// The iterator represents a position which shows the box that is bigger than the order, and the one that is smaller than the order
	while ($widgetPacks[$iterator] > $order) {
		$iterator++;
	}
	
	// If the order is the same as one of the boxes, order the box
	if ($widgetPacks[$iterator] == $order) {
		return array($order);
	// The stop condition of the recursive algorithm. When it reaches the end of the recursive calls, it finds the most efficient way to buy boxes. 
	// Either 2 small ones, or one big one.
	} elseif ($order - $widgetPacks[$iterator] < $widgetPacks[ARRAYLENGTH - 1]) {
		$smallPacks = array($widgetPacks[ARRAYLENGTH - 1], $widgetPacks[ARRAYLENGTH - 1]);
		$mixedPacks = array($widgetPacks[ARRAYLENGTH - 1], $widgetPacks[$iterator]);
		
		// The function which checks which option orders less extra widgets.
		$bestPack = betterPack($smallPacks, $mixedPacks, $order);
		if (($widgetPacks[$iterator - 1] - $order) <= (array_sum($bestPack) - $order)) {
			return array($widgetPacks[$iterator - 1]);
		} else {
			return $bestPack;
		}
	}
	
	// The recursive call of the function
	$returnedArray = packsToSend($order - $widgetPacks[$iterator], $iterator, $widgetPacks);
	
	// This checks whether buying more small boxes has had less extra widgets, or whether buying a big box will.
	if (($widgetPacks[$iterator - 1] - $order) <= (array_sum($returnedArray) - $order + $widgetPacks[$iterator])) {
		return array ($widgetPacks[$iterator - 1]);
	} else {
		array_push ($returnedArray, $widgetPacks[$iterator]);
		return $returnedArray;
	}
}

// The function which checks which option orders less extra widgets.
function betterPack($smallPacks, $mixedPacks, $order){
	if(abs($order - array_sum($smallPacks)) < abs($order - array_sum($mixedPacks))) {
		return $smallPacks;
	} else {
		return $mixedPacks;
	}
}

?>
</body>
</html>