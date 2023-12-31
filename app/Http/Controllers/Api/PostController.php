<?php

namespace App\Http\Controllers\Api;
use App\Http\Requests\Api\PostStoreRequest;
use App\Http\Requests\Api\PostUpdateRequest;
use App\Http\Requests\Api\PostStoreMediaRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\PostResource;
use App\Models\Post;
// use Cloudinary\Api\Upload\UploadApi;
// use Cloudinary\Cloudinary;
// use Cloudinary\Configuration\Configuration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class PostController extends Controller
{
    protected $uploadPath = 'uploads/post';
    /**
     * @OA\Get(
     *      path="/posts",
     *      operationId="getPostsList",
     *      tags={"Posts"},
     *      summary="Get list of posts",
     *      description="Returns list of posts",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(name="page", in="query", description="the page number", required=false,
     *        @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *     )
     */
    public function index()
    {
        return Post::paginate(10);
    }

    /**
     * @OA\Post(
     *      path="/posts",
     *      operationId="storePost",
     *      tags={"Posts"},
     *      summary="Store new post",
     *      description="Returns post data",
     *      security={{"bearerAuth":{}}},
     *      @OA\RequestBody(
     *          @OA\JsonContent()
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     * )
     */
    public function store(PostStoreRequest $request)
    {
        $imageName = '';
        if($request->hasFile('image')) {
            $imageName = time().'.'.$request->image->extension();
            $request->image->move(public_path($this->uploadPath), $imageName);
        }

        $post = Post::create([
            'user_id' => Auth::user()->id,
            'title' => $request->title,
            'image' => $imageName,
            'category_id' => $request->category_id,
            'slug' => $request->slug,
            'detail' => $request->detail,
            'status' => $request->status
        ]);

        return new PostResource($post);
    }


    /**
     * @OA\Get(
     *      path="/posts/{id}",
     *      operationId="getPostById",
     *      tags={"Posts"},
     *      summary="Get post information",
     *      description="Returns post data",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Post id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     * )
     */
    public function show(Post $post)
    {
        return new PostResource($post);
    }

    /**
     * @OA\Put(
     *      path="/posts/{id}",
     *      operationId="updatePost",
     *      tags={"Posts"},
     *      summary="Update existing post",
     *      description="Returns updated post data",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Post id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          @OA\JsonContent()
     *      ),
     *      @OA\Response(
     *          response=202,
     *          description="Successful operation",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      )
     * )
     */
    public function update(PostUpdateRequest $request, Post $post)
    {

        if (Auth::user()->id !== $post->user_id) {
            return response()->json(['error' => 'You can only edit your own posts.'], 403);
        }

        $post->update($request->only(['title', 'category_id', 'slug', 'detail', 'status']));

        return new PostResource($post);
    }

     /**
     * @OA\Delete(
     *      path="/posts/{id}",
     *      operationId="deletePost",
     *      tags={"Posts"},
     *      summary="Delete existing post",
     *      description="Deletes a record and returns no content",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Post id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="Successful operation",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      )
     * )
     */
    public function destroy(Post $post)
    {
        if (Auth::user()->id !== $post->user_id) {
            return response()->json(['error' => 'You can only delete your own posts.'], 403);
        }
        
        $post->delete();
        return response()->json(null, 204);
    }

    public function storeMedia(PostStoreMediaRequest $request, Post $post)
    {
        if (Auth::user()->id !== $post->user_id) {
            return response()->json(['error' => 'You can only upload your own posts.'], 403);
        }

        if($request->hasFile('image')) {
            if ($post->image) {
                Storage::delete(public_path($this->uploadPath) . $post->image);
            }
            $imageName = time().'.'.$request->image->extension();
            $request->image->move(public_path($this->uploadPath), $imageName);
        }else{
            $imageName = $post->image;
        }
        $post->update(['image' => $imageName]);

        return new PostResource($post);
    }
}
