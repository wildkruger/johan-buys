use Illuminate\Console\Command;
use App\Models\User;
use App\Services\PaywebService;

class PaymoneyTransactions extends Command
{
    protected $signature = 'paymoney:transactions';
    protected $description = 'Process Paymoney transactions';

    public function handle()
    {
        $users = User::all();

        foreach ($users as $user) {
            // Deposit Logic
            $paywebService = new PaywebService();
            $depositAmount = 100; // Example deposit amount
            $paywebService->initiateDeposit($user->paywebApiCode, $depositAmount);
            $user->balance += $depositAmount;
            $user->save();

            // Refund Logic
            $refundAmount = 50; // Example refund amount
            if ($refundAmount <= $user->balance) {
                $paywebService->initiateRefund($user->paywebApiCode, $refundAmount);
                $user->balance -= $refundAmount;
                $user->save();
            }
        }
    }
}