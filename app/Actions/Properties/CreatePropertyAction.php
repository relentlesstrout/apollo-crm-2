<?php

namespace App\Actions\Properties;

use App\Actions\Customers\RecomputeCustomerStatusAction;
use App\DTOs\Property\PropertyData;
use App\Models\Customer;
use App\Models\Property;
use Illuminate\Support\Facades\Http;

class CreatePropertyAction
{
    public function __construct(private readonly RecomputeCustomerStatusAction $recomputeStatus) {}

    public function execute(Customer $customer, PropertyData $data): Property
    {

        //        $address = $data->house . ', ' . $data->street . ', ' . $data->postcode;
        //        $response = Http::get('https://nominatim.openstreetmap.org/search', [
        //            'q' => $address,
        //            'format' => 'json',
        //            'limit' => 1,
        //        ]);
        //
        //        $result = $response->json()[0];

        // Save Lt/Lng to DB
        // $lat = $result['lat'];
        // $lon = $result['lon'];

        $property = Property::create([
            'customer_id' => $customer->id,
            'house' => $data->house,
            'street' => $data->street,
            'area' => $data->area,
            'postcode' => $data->postcode,
            'notes' => $data->notes,
        ]);

        $this->recomputeStatus->execute($customer);

        return $property;
    }
}
