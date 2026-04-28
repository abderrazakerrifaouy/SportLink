<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::transaction(function (): void {
            $now = now();

            $users = $this->seedUsers(200, $now);
            $clubAdminUserIds = array_slice($users, 0, 100);
            $joueurUserIds = array_slice($users, 100, 100);

            $clubAdmins = $this->seedClubAdmins($clubAdminUserIds, $now);
            $joueurs = $this->seedJoueurs($joueurUserIds, $now);
            $posts = $this->seedPosts($users, $now);
            $comments = $this->seedComments($users, $posts, $now);
            $replies = $this->seedReplies($users, $comments, $now);
            $this->seedMedia($posts, $now);
            $this->seedFollows($users, $now);
            $this->seedMessages($users, $now);
            $experiences = $this->seedExperiences($clubAdmins, $joueurs, $now);
            $this->seedClubJoueurRequests($clubAdmins, $joueurs, $experiences, $now);
            $this->seedTitres($clubAdmins, $now);
            $this->seedReactions($users, $posts, $comments, $replies, $now);
            $this->seedReports($users, $posts, $comments, $replies, $now);
        });
    }

    private function seedUsers(int $count, $now): array
    {
        $roles = ['JOUEUR', 'CLUB_ADMIN', 'ADMIN'];
        $userIds = [];

        for ($index = 0; $index < $count; $index++) {
            $userIds[] = DB::table('users')->insertGetId([
                'name' => fake()->name(),
                'email' => 'seed-user-' . Str::uuid() . '@example.test',
                'password' => bcrypt('password'),
                'bio' => fake()->optional()->paragraph(),
                'profileImage' => fake()->optional()->imageUrl(400, 400, 'people', true),
                'bannerImage' => fake()->optional()->imageUrl(1200, 400, 'sports', true),
                'role' => $roles[$index % count($roles)],
                'isActive' => true,
                'remember_token' => Str::random(10),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        return $userIds;
    }

    private function seedClubAdmins(array $userIds, $now): array
    {
        $clubAdminIds = [];

        foreach ($userIds as $index => $userId) {
            $clubAdminIds[] = DB::table('club_admins')->insertGetId([
                'user_id' => $userId,
                'nomClub' => fake()->unique()->company() . ' FC ' . ($index + 1),
                'description' => Str::limit(fake()->paragraph(), 255, ''),
                'ecole' => fake()->company() . ' Academy',
                'tactique' => fake()->randomElement(['4-3-3', '4-4-2', '3-5-2', '4-2-3-1']),
                'gestion' => fake()->randomElement(['OFFENSIVE', 'BALANCED', 'DEFENSIVE']),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        return $clubAdminIds;
    }

    private function seedJoueurs(array $userIds, $now): array
    {
        $joueurIds = [];

        foreach ($userIds as $userId) {
            $joueurIds[] = DB::table('joueurs')->insertGetId([
                'user_id' => $userId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        return $joueurIds;
    }

    private function seedPosts(array $userIds, $now): array
    {
        $postIds = [];

        for ($index = 0; $index < 100; $index++) {
            $postIds[] = DB::table('posts')->insertGetId([
                'content' => fake()->paragraph(4),
                'user_id' => $userIds[$index % count($userIds)],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        return $postIds;
    }

    private function seedComments(array $userIds, array $postIds, $now): array
    {
        $commentIds = [];

        for ($index = 0; $index < 100; $index++) {
            $commentIds[] = DB::table('comments')->insertGetId([
                'content' => fake()->sentence(12),
                'user_id' => $userIds[($index + 17) % count($userIds)],
                'post_id' => $postIds[$index % count($postIds)],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        return $commentIds;
    }

    private function seedReplies(array $userIds, array $commentIds, $now): array
    {
        $replyIds = [];

        for ($index = 0; $index < 100; $index++) {
            $replyIds[] = DB::table('replies')->insertGetId([
                'content' => fake()->sentence(10),
                'user_id' => $userIds[($index + 33) % count($userIds)],
                'comment_id' => $commentIds[$index % count($commentIds)],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        return $replyIds;
    }

    private function seedMedia(array $postIds, $now): array
    {
        $mediaIds = [];

        for ($index = 0; $index < 100; $index++) {
            $mediaIds[] = DB::table('media')->insertGetId([
                'url' => 'https://picsum.photos/seed/' . Str::uuid() . '/1200/800',
                'mediaType' => fake()->randomElement(['IMAGE', 'VIDEO']),
                'post_id' => $postIds[$index % count($postIds)],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        return $mediaIds;
    }

    private function seedFollows(array $userIds, $now): array
    {
        $followIds = [];
        $totalUsers = count($userIds);

        for ($index = 0; $index < 100; $index++) {
            $followIds[] = DB::table('follows')->insertGetId([
                'follower_id' => $userIds[$index],
                'following_id' => $userIds[($index + 101) % $totalUsers],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        return $followIds;
    }

    private function seedMessages(array $userIds, $now): array
    {
        $messageIds = [];
        $totalUsers = count($userIds);

        for ($index = 0; $index < 100; $index++) {
            $messageIds[] = DB::table('messages')->insertGetId([
                'message' => fake()->sentence(14),
                'sender_id' => $userIds[$index],
                'receiver_id' => $userIds[($index + 50) % $totalUsers],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        return $messageIds;
    }

    private function seedExperiences(array $clubAdminIds, array $joueurIds, $now): array
    {
        $experienceIds = [];
        $categories = ['SENIOR', 'ESPOIR', 'JUNIOR', 'CADET', 'MINIM'];

        for ($index = 0; $index < 100; $index++) {
            $joinDate = fake()->dateTimeBetween('-8 years', '-1 year');
            $endDate = fake()->optional(0.7)->dateTimeBetween($joinDate, 'now');

            $experienceIds[] = DB::table('experiences')->insertGetId([
                'idClub' => $clubAdminIds[$index % count($clubAdminIds)],
                'image' => fake()->optional()->imageUrl(800, 600, 'sports', true),
                'joinDate' => $joinDate->format('Y-m-d'),
                'endDate' => $endDate ? $endDate->format('Y-m-d') : null,
                'place' => fake()->city(),
                'categoryType' => $categories[$index % count($categories)],
                'joueur_id' => $joueurIds[$index % count($joueurIds)],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        return $experienceIds;
    }

    private function seedClubJoueurRequests(array $clubAdminIds, array $joueurIds, array $experienceIds, $now): array
    {
        $requestIds = [];
        $statuses = ['PENDING', 'ACCEPTED', 'REJECTED'];

        for ($index = 0; $index < 100; $index++) {
            $requestIds[] = DB::table('club_joueur_requests')->insertGetId([
                'joueur_id' => $joueurIds[$index % count($joueurIds)],
                'club_admin_id' => $clubAdminIds[$index % count($clubAdminIds)],
                'status' => $statuses[$index % count($statuses)],
                'experience_id' => $experienceIds[$index % count($experienceIds)],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        return $requestIds;
    }

    private function seedTitres(array $clubAdminIds, $now): array
    {
        $titreIds = [];

        for ($index = 0; $index < 100; $index++) {
            $titreIds[] = DB::table('titres')->insertGetId([
                'nomTitre' => fake()->unique()->words(3, true),
                'image' => fake()->optional()->imageUrl(600, 600, 'sports', true),
                'description' => fake()->paragraph(),
                'number' => fake()->numberBetween(1, 20),
                'club_admin_id' => $clubAdminIds[$index % count($clubAdminIds)],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        return $titreIds;
    }

    private function seedReactions(array $userIds, array $postIds, array $commentIds, array $replyIds, $now): array
    {
        $reactionIds = [];
        $types = ['LIKE', 'HAHA', 'WOW', 'GRR', 'SAD', 'LOVE', 'DISLIKE'];
        $targets = [
            ['type' => 'App\\Models\\Post', 'ids' => $postIds],
            ['type' => 'App\\Models\\Comment', 'ids' => $commentIds],
            ['type' => 'App\\Models\\Reply', 'ids' => $replyIds],
        ];

        for ($index = 0; $index < 100; $index++) {
            $target = $targets[$index % count($targets)];

            $reactionIds[] = DB::table('reactions')->insertGetId([
                'type' => $types[$index % count($types)],
                'user_id' => $userIds[$index % count($userIds)],
                'reactable_type' => $target['type'],
                'reactable_id' => $target['ids'][$index % count($target['ids'])],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        return $reactionIds;
    }

    private function seedReports(array $userIds, array $postIds, array $commentIds, array $replyIds, $now): array
    {
        $reportIds = [];
        $targets = [
            ['type' => 'App\\Models\\Post', 'ids' => $postIds],
            ['type' => 'App\\Models\\Comment', 'ids' => $commentIds],
            ['type' => 'App\\Models\\Reply', 'ids' => $replyIds],
        ];
        $statuses = ['pending', 'under_review', 'resolved'];

        for ($index = 0; $index < 100; $index++) {
            $target = $targets[$index % count($targets)];
            $status = $statuses[$index % count($statuses)];
            $resolvedAt = $status === 'resolved' ? $now->copy()->subDays(random_int(0, 14)) : null;

            $reportIds[] = DB::table('reports')->insertGetId([
                'reporter_id' => $userIds[($index + 11) % count($userIds)],
                'reportable_type' => $target['type'],
                'reportable_id' => $target['ids'][$index % count($target['ids'])],
                'reason' => fake()->sentence(10),
                'status' => $status,
                'reviewed_by' => $status === 'pending' ? null : $userIds[($index + 77) % count($userIds)],
                'resolved_at' => $resolvedAt,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        return $reportIds;
    }
}
