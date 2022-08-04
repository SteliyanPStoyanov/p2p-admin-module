<?php


namespace Modules\Admin\Repositories;


use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Entities\BlogPage;
use Modules\Core\Repositories\BaseRepository;

class BlogPageRepository extends BaseRepository
{

    /**
     * @param int $blogPageId
     *
     * @return mixed
     */
    public function getById(int $blogPageId)
    {
        return BlogPage::where(
            'blog_page_id',
            '=',
            $blogPageId
        )->first();
    }

    /**
     * @param BlogPage $blogPage
     *
     * @throws \Exception
     */
    public function delete(BlogPage $blogPage)
    {
        $blogPage->delete();
    }

    /**
     * @param array $data
     *
     * @return BlogPage
     */
    public function create(array $data)
    {
        $blogPage = new BlogPage();
        $blogPage->fill($data);
        $blogPage->save();

        return $blogPage;
    }

    /**
     * @param BlogPage $blogPage
     * @param array $data
     *
     * @return BlogPage
     */
    public function update(BlogPage $blogPage, array $data)
    {
        $blogPage->fill($data);
        $blogPage->save();

        return $blogPage;
    }

    /**
     * @param int $limit
     * @param array $where
     * @param array|string[] $order
     * @param bool $showDeleted
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAll(
        int $limit,
        array $where = [],
        array $order = ['blog_page.active' => 'DESC', 'blog_page_id' => 'DESC'],
        bool $showDeleted = false
    ) {
        $builder = DB::table('blog_page');
        $builder->select(
            DB::raw('blog_page.*')
        );

        $builder->leftJoin('administrator', 'administrator.administrator_id', '=', 'blog_page.administrator_id');


        if (!empty($where['tags'])) {
            $builder->whereJsonContains('tags->tag', $where['tags']);
            unset($where['tags']);
        }

        if (!empty($where)) {
            $builder->where($where);
        }

        if (!empty($order['tags'])) {
            $builder->orderBy('tags->tag', $order['tags']);
            unset($order['tags']);
        }

        if (!empty($order)) {
            foreach ($order as $key => $direction) {
                $builder->orderBy($key, $direction);
            }
        }

        $result = $builder->paginate($limit);
        $records = BlogPage::hydrate($result->all());
        $result->setCollection($records);

        return $result;
    }

    /**
     * @param BlogPage $blogPage
     */
    public function enable(BlogPage $blogPage)
    {
        $blogPage->enable();
    }

    /**
     * @param BlogPage $blogPage
     */
    public function disable(BlogPage $blogPage)
    {
        $blogPage->disable();
    }

    /**
     * @return array
     */
    public function getArchives()
    {
        return $results = DB::select(
            DB::raw(
                "
                    select  to_char(date_trunc('month', date)::date, 'Month') as month,
                           to_char(date_trunc('year', date)::date, 'YYYY') as year,
                           count(*) published
                    from blog_page
                    where blog_page.deleted = 0
                    and blog_page.active = 1
                    group by date_trunc('month', date),date_trunc('year', date)
                    ORDER BY date_trunc('month', date) asc;
                    "
            ),
        );
    }

    /**
     * @param string $year
     * @param string $month
     */
    public function getByArchive(string $month)
    {
        $startOfMonth = Carbon::parse($month)->startOfMonth()->toDateString();
        $endOfMonth = Carbon::parse($month)->endOfMonth()->toDateString();

        return BlogPage::select('*')
            ->whereBetween(
                'date',
                [
                    $startOfMonth,
                    $endOfMonth
                ]
            )->get();
    }
}
