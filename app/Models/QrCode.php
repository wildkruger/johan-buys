<?php



namespace App\Models;



use Illuminate\Support\Str;

use Illuminate\Database\Eloquent\Model;



class QrCode extends Model

{

	protected $table = 'qr_codes';



    protected $fillable = ['object_id', 'object_type', 'secret', 'status'];



    public function user()

    {

        return $this->belongsTo(User::class, 'user_id');

    }



    /**

     * Create User QR code

     *

     * @param object $user

     * @return void

     */

     public function createUserQrCode($user, $objectType = 'user')

    {

        $qrCode = self::where(['object_id' => $user->id, 'object_type' => $objectType, 'status' => 'Active'])->first(['id']);

        if (empty($qrCode))

        {

            $createInstanceOfQrCode              = new self();

            $createInstanceOfQrCode->object_id   = $user->id;

            $createInstanceOfQrCode->object_type = $objectType;

            if (!empty($user->formattedPhone)) {

                $createInstanceOfQrCode->secret = convert_string('encrypt', $createInstanceOfQrCode->object_type . '-' . $user->email . '-' . $user->formattedPhone . '-' . Str::random(6));

            } else {

                $createInstanceOfQrCode->secret = convert_string('encrypt', $createInstanceOfQrCode->object_type . '-' . $user->email . '-' . Str::random(6));

            }

            $createInstanceOfQrCode->status = 'Active';

            $createInstanceOfQrCode->save();

        }

    }

    /**
     * Create or update a QR code for a given object type
     *
     * @param object $user
     * @param string $objectType
     * @param string $publicUrl
     * @return void
     * @throws \Exception
     */
    public function updateOrCreateQrCode($user, string $objectType, string $publicUrl)
    {
        try {
            if (!isset($user->id)) {
                throw new \InvalidArgumentException("User ID is required");
            }

            if (empty($objectType)) {
                throw new \InvalidArgumentException("Object type is required");
            }

            $secret = convert_string('encrypt', $publicUrl);

            // Create or update the QR code in the database
            return self::updateOrCreate(
                [
                    'object_id' => $user->id,
                    'object_type' => $objectType,
                ],
                [
                    'secret' => $secret,
                    'status' => 'Active',
                ]
            );

        } catch (\Exception $e) {
            // Handle exceptions as needed
            throw new \Exception("Error updating or creating QR code: " . $e->getMessage());
        }
    }
}

