<?php
namespace nWooCommerce\Order;

use \Greystar\User as Greystar;
use \Greystar\WP\User; // Extends \nWordpress\User
use \nWooCommerce\Products;
use \nWordpress\ArrayWorks;

class Controller extends \nWooCommerce\Order
{
	public	function createCustomOrdering($order_id)
	{
		# Wordpress User
		$User			=	new User();
		# Instance of Greystar
		$Greystar		=	new Greystar();
		# Used to map two arrays
		$ArrayWorks		=	new ArrayWorks();
		# Get the order data
		$order			=	$this->getOrder($order_id);
		# Get logged in user id
		$user_id		=	$User->getUserId();
		# Create a meta key for this order
		$order_meta		=	'gs_invoice_'.$order_id;
		# Fetch the distributor id from WP to check if the distributor is new or current
		$distid			=	get_user_meta($user_id, 'distid');
		# Fetch the enroller id to recorder orders for crediting
		$enroller		=	$User->getEnrollerId();
		# Mapping
		$mapPath		=	THEME_CLIENT_SETTINGS.DS.'mapping'.DS;
		# If one is created already, assign it
		if(!empty($distid[0]))
			$distid	=	$distid[0];
		# Create the distributorship if not already done
		else {
			# Get the mapping array
			$Map		=	$this->getHelper('nRegister')->parseXmlFile($mapPath.'gs.create.xml');
			# Set the map
			$distMap	=	$ArrayWorks->mapFields($Map,$order);
			# Possible there is no shipping address
			if(count($distMap) <= 6) {
				# Get the mapping array
				$Map		=	$this->getHelper('nRegister')->parseXmlFile($mapPath.'gs.create_billing.xml');
				# Set the map
				$distMap	=	$ArrayWorks->mapFields($Map,$order);
			}
			# Check username
			$userExists	=	$Greystar->userExists($distMap['username']);
			# If user exits already, make a new userid with datestamp
			if($userExists)
				$distMap['username']	=	$distMap['username'].date('Ymdhis');
			# Create a new distributorship
			$createDist	=	$Greystar->create($distMap);
			# Check if the distributor id is created
			if(!empty($createDist['internal_id'])) {
				# Assign the distid
				$distid	=	$createDist['internal_id'];
				# Add distributor id
				add_user_meta($user_id,'distid', $distid, true);
				# Save a credit
				add_user_meta($user_id,'beyond_enroll_credit',$enroller);
			}
		}
		# Get the raw object to get meta
		$Order		=	$this->getRawOrder();
		# Get invoice meta
		$Invoices	=	$Order->get_meta($order_meta);
		# If there is already a meta for this order, stop
		if(!empty($Invoices))
			return false;
		# Get the invoice map
		$MapOrder			=	$this->getHelper('nRegister')->parseXmlFile($mapPath.'gs.invoice.xml');
		# Assign distributor id
		$order['username']	=	$distid;
		# Create a mapping for order data
		$order				=	$ArrayWorks->mapFields($MapOrder,$order);
		# Go through this order looking for credits
		foreach($order as $key => $value) {
			# If there is a product key
			if(stripos($key,'product') !== false) {
				# Fetch the product from the stored items using sku as the match key
				$prod	=	(new Products)->getCurrent($value,'sku');
				# If no sku matching, continue
				# If the object has no credit value, stop
				if(empty($prod) || empty($prod['attributes']['credit']))
					continue;
				# Get the values from there
				$credit	=	$prod['attributes']['credit']->get_options();
				# Add all credit values
				if(is_array($credit))
					$credit	=	array_sum($credit);
				# Fetch user's current credit
				$beyondBucks	=	get_user_meta($user_id,'beyond_bucks');
				# Convert array to values
				if(is_array($beyondBucks))
					$beyondBucks	=	array_sum($beyondBucks);
				# Strip out the "product" from the key, make new qty key
				$qty	=	'qty'.preg_replace('/[^0-9]/','',$key);
				# Multiply credits by qty
				$bucks	=	(floatval($order[$qty]) * floatval($credit));
				# If there are no credits
				if(empty($beyondBucks)){
					# Make credits
					$beyondBucks	=	$bucks;
				}
				else {
					# Add credits
					$beyondBucks	+=	$bucks;
				}
				# Save the credits to file
				update_user_meta($user_id,'beyond_bucks',$beyondBucks);
			}
		}
		# Create GS invoice from mapped array 
		$invoice	=	$Greystar->invoice($order);
		# Store the GS invoice the the WC Order meta
		$Order->add_meta_data($order_meta,$invoice['invoice']);
		# Save the order to keep that value stored
		$Order->save();
	}
}