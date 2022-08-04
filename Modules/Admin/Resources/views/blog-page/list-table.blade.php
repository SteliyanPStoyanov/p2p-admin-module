<table class="table">
    <thead>
    <tr>
        <th scope="col">{{__('common.Id')}}</th>
        <th scope="col">{{__('common.BlogPageTitle')}}</th>
        <th scope="col">{{__('common.BlogPageContent')}}</th>
        <th scope="col">{{__('common.Active')}}</th>
        <th scope="col">{{__('common.Date')}}</th>
        <th scope="col">{{__('common.Deleted')}}</th>
        <th scope="col">{{__('common.CreatedAt')}}</th>
        <th scope="col">{{__('common.CreatedBy')}}</th>
        <th scope="col">{{__('common.UpdatedAt')}}</th>
        <th scope="col">{{__('common.UpdatedBy')}}</th>
        <th scope="col">{{__('common.Actions')}}</th>
    </tr>
    </thead>
    <tbody id="blogPageTable">
    @foreach($blogPages as $blogPage)
        <tr>
            <td>{{ $blogPage->blog_page_id }}</td>
            <td>{{ $blogPage->title }}</td>
            <td>{{ substr($blogPage->content, 0, 50) }}</td>
            <td>{{ $blogPage->active ? __('common.Yes') : __('common.No') }}</td>
            <td>{{showDate($blogPage->date)}}</td>
            <td>{{ $blogPage->deleted ? __('common.Yes') : __('common.No') }}</td>
            <x-timestamps :model="$blogPage"/>
            @if($blogPage->deleted === 1)
                <td class="button-div">
                    <div class="button-actions">
                        <x-btn-edit
                            url="{{ route('admin.blog-page.edit', $blogPage->blog_page_id) }}"/>
                    </div>
                </td>
            @else
                <td class="button-div">
                    <div class="button-actions">
                        <x-btn-edit
                            url="{{ route('admin.blog-page.edit', $blogPage->blog_page_id) }}"/>
                        <x-btn-delete
                            url="{{ route('admin.blog-page.delete', $blogPage->blog_page_id) }}"/>
                        @if($blogPage->active)
                            <x-btn-disable
                                url="{{ route('admin.blog-page.disable', $blogPage->blog_page_id) }}"/>
                        @else
                            <x-btn-enable
                                url="{{ route('admin.blog-page.enable', $blogPage->blog_page_id) }}"/>
                        @endif
                    </div>
                </td>
            @endif
        </tr>
    @endforeach
    </tbody>
    <tfoot>
    <tr id="pagination-nav">
        <td colspan="10">
            {{ $blogPages->links() }}
        </td>
    </tr>
    </tfoot>
</table>
