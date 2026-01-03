<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\GroupLocation;

class CleanupDuplicateLocations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'locations:cleanup-duplicates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'حذف المواقع المكررة وإبقاء آخر موقع فقط لكل user في كل group';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 جاري حذف المواقع المكررة...');

        // Get all unique combinations of group_id and user_id
        $combinations = DB::table('group_locations')
            ->select('group_id', 'user_id')
            ->groupBy('group_id', 'user_id')
            ->havingRaw('COUNT(*) > 1') // Only combinations with duplicates
            ->get();

        if ($combinations->isEmpty()) {
            $this->info('✅ لا توجد مواقع مكررة!');
            return 0;
        }

        $this->info("📊 تم العثور على {$combinations->count()} مجموعة مكررة");

        $totalDeleted = 0;
        $bar = $this->output->createProgressBar($combinations->count());
        $bar->start();

        foreach ($combinations as $combo) {
            // Get all locations for this group_id + user_id, ordered by updated_at DESC
            $locations = GroupLocation::where('group_id', $combo->group_id)
                ->where('user_id', $combo->user_id)
                ->orderBy('updated_at', 'desc')
                ->get();

            // Keep the first one (most recent), delete the rest
            $toKeep = $locations->first();
            $toDelete = $locations->slice(1);

            foreach ($toDelete as $location) {
                $location->delete();
                $totalDeleted++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("✅ تم حذف {$totalDeleted} موقع مكرر بنجاح!");
        $this->info("📍 تم الاحتفاظ بآخر موقع فقط لكل user في كل group");

        return 0;
    }
}
