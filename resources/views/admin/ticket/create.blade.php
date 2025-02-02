@extends('admin.index')
@section('content')
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="mb-4">افزودن محصول</h5>

            <form method="post" action="/admin/product/save" enctype="multipart/form-data">
                @csrf
                <label class="form-group has-float-label">
                    <input  type="text" name="title" class="form-control" value="{{old('title')}}" />
                    <span>عنوان</span>
                </label>
                @error('title')
                <div class="alert alert-danger">{{ $message }}</div>
                @enderror
                <label>دسته بندی</label>
                <select class="form-control" data-width="100%" name="category_id">
                    @foreach($categories as $category)
                        <option value="{{$category->id}}" @if(old('category_id')==$category->id) selected @endif>{{$category->name}}</option>
                    @endforeach
                </select>
                @error('category_id')
                <div class="alert alert-danger">{{ $message }}</div>
                @enderror
                <label class="mt-2">برند</label>
                <select class="form-control" data-width="100%" name="brand_id">
                    @foreach($brands as $brand)
                        <option value="{{$brand->id}}" @if(old('brand_id')==$brand->id) selected @endif>{{$brand->name}}</option>
                    @endforeach
                </select>
                @error('brand_id')
                <div class="alert alert-danger">{{ $message }}</div>
                @enderror
                <label class="mt-3">تصویر</label>
                <br>
                <input type="file" name="file">
                @error('file')
                <div class="alert alert-danger">{{ $message }}</div>
                @enderror
                <label class="form-group has-float-label mt-4">
                    <input type="number" name="price" class="form-control" value="{{old('price')}}"/>
                    <span>قیمت</span>
                </label>
                @error('price')
                <div class="alert alert-danger">{{ $message }}</div>
                @enderror
                <label class="form-group has-float-label">
                    <input type="text" name="color" class="form-control" value="{{old('color')}}"/>
                    <span>رنگ</span>
                </label>
                @error('color')
                <div class="alert alert-danger">{{ $message }}</div>
                @enderror
                <label class="form-group has-float-label">
                    <input type="text" name="description" class="form-control" value="{{old('description')}}" />
                    <span>توضیحات</span>
                </label>
                @error('description')
                <div class="alert alert-danger">{{ $message }}</div>
                @enderror
                <label class="form-group has-float-label">
                    <input type="number" name="stock" class="form-control" value="{{old('stock')}}" />
                    <span>موجودی کالا</span>
                </label>
                @error('stock')
                <div class="alert alert-danger">{{ $message }}</div>
                @enderror
                <button class="btn btn-primary mt-3" type="submit">افزودن</button>
            </form>
        </div>
    </div>

@endsection

