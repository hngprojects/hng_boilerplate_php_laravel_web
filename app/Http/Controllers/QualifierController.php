namespace App\Http\Controllers;

use Illuminate\Http\Request;

class QualifierController extends Controller
{
    public function index()
    {
        // Retrieve the list of available qualifiers
        $qualifiers = // implement the logic to retrieve the qualifiers

        return response()->json($qualifiers);
    }
}
