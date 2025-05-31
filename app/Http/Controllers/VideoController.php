<?php

namespace App\Http\Controllers;

use App\Models\Video; // Videoモデル
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
// use App\DataTransferObjects\VideoData; // DTOを使う場合

class VideoController extends Controller
{
    // 動画アップロードフォームを表示
    public function create()
    {
        return view('videos.create');
    }

    // 動画アップロード処理
    public function store(Request $request)
    {
        // 1. バリデーション (重要！)
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'local_video_path' => 'nullable|string', // このパスの扱いと検証は慎重に
            'video_file' => 'required_without:local_video_path|file|mimetypes:video/avi,video/mpeg,video/quicktime,video/mp4|max:1024000', // 例: 1GBまで、mp4など
            'thumbnail_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // 例: 2MBまで
            'visibility' => 'required|in:private,unlisted,public',
        ]);

        $videoPath = null;
        $thumbnailPath = null;
        $user = Auth::user();

        try {
            // 2. ファイル処理
            // A. 標準的なファイルアップロードの場合
            if ($request->hasFile('video_file')) {
                // ファイル名にランダムな文字列を付加するか、ユーザーID/日時などでユニークにする
                // $filename = Str::uuid() . '.' . $request->file('video_file')->getClientOriginalExtension();
                // $videoPath = $request->file('video_file')->storeAs('videos', $filename, 'public');
                $videoPath = $request->file('video_file')->store('videos/' . $user->id, 'public'); // videos/user_id/filename.ext のように保存
            }
            // B. "Local Path" が指定された場合 (サーバーサイドのパス)
            elseif (!empty($validatedData['local_video_path'])) {
                $localPath = $validatedData['local_video_path'];
                if (file_exists($localPath) && is_readable($localPath)) {
                    $fileContents = file_get_contents($localPath);
                    $originalExtension = pathinfo($localPath, PATHINFO_EXTENSION);
                    $newFileName = Str::uuid() . '.' . $originalExtension;
                    $videoPath = "videos/{$user->id}/{$newFileName}"; // 保存パス
                    Storage::disk('public')->put($videoPath, $fileContents);
                } else {
                    // エラー処理: ファイルが見つからない、または読み取れない
                    return back()->withErrors(['local_video_path' => 'Specified local video file not found or is not readable.'])->withInput();
                }
            } else {
                // どちらの動画ソースも提供されていない場合 (バリデーションでカバーされるべきだが念のため)
                return back()->withErrors(['video_file' => 'A video file or a local server path is required.'])->withInput();
            }

            // サムネイルファイルの処理
            if ($request->hasFile('thumbnail_file')) {
                $thumbnailPath = $request->file('thumbnail_file')->store('thumbnails/' . $user->id, 'public');
            }

            // 3. データベースに保存
            $video = Video::create([
                'user_id' => $user->id,
                'title' => $validatedData['title'],
                'description' => $validatedData['description'],
                'video_path' => $videoPath,
                'thumbnail_path' => $thumbnailPath,
                'visibility' => $validatedData['visibility'],
                'status' => 'processing', // 例: アップロード直後は処理中ステータス
                // duration などは、後続のジョブで動画を処理して取得・更新する
            ]);

            // 成功メッセージと共にリダイレクト (例: 動画詳細ページやダッシュボードへ)
            return redirect()->route('dashboard')->with('success', 'Video uploaded successfully! It is now being processed.'); // 'dashboard' は適切なルート名に

        } catch (\Exception $e) {
            // エラーログを記録
            logger()->error('Video upload failed: ' . $e->getMessage());

            // アップロードされた可能性のあるファイルを削除 (クリーンアップ)
            if ($videoPath && Storage::disk('public')->exists($videoPath)) {
                Storage::disk('public')->delete($videoPath);
            }
            if ($thumbnailPath && Storage::disk('public')->exists($thumbnailPath)) {
                Storage::disk('public')->delete($thumbnailPath);
            }

            return back()->with('error', 'Video upload failed. Please try again.')->withInput();
        }
    }
}