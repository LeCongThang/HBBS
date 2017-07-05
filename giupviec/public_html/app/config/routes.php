<?php 

/**
 * Used to define the routes in the system.
 * 
 * A route should be defined with a key matching the URL and an
 * controller#action-to-call method. E.g.:
 * 
 * '/' => 'index#index',
 * '/calendar' => 'calendar#index'
 */
$routes = array(
	'/' => 'index#index',

    '/contact' => 'index#contact',
    '/idols' => 'index#idol',
    '/news' => 'news#index',
    '/login' => 'index#login',
    '/search' => 'index#search',
    '/lich-hoc' => 'index#lich',
    '/logout' => 'index#logout',


    '/program/:slug'  => 'index#program',
    '/category/:slug'  => 'news#index',
    '/idol/:slug'  => 'news#idol',
    '/gallery/:slug'  => 'gallery#images',
    '/gallery' => 'gallery#image',
    '/gallery-video' => 'gallery#video',
    '/:slug' => 'news#detail',

    '/admin' => 'admin\index#index',
    '/admin/' => 'admin\index#index',
    '/admin/news/create-video' => 'admin\news#video',
    '/admin/news/update-video' => 'admin\news#updateVideo',
    '/admin/news' => 'admin\news#index',
    '/admin/news/create-news' => 'admin\news#create',
    '/admin/news/update-news' => 'admin\news#update',
    '/admin/news/delete' => 'admin\news#delete',
    '/admin/gallery-video' => 'admin\gallery#video',
    '/admin/gallery-video/delete' => 'admin\gallery#delete',
    '/admin/gallery' => 'admin\gallery#index',
    '/admin/gallery/create' => 'admin\gallery#create',
    '/admin/gallery/update' => 'admin\gallery#update',
    '/admin/gallery/delete' => 'admin\gallery#del',

    '/admin/category' => 'admin\category#index',
    '/admin/category/create' => 'admin\category#create',
    '/admin/category/update' => 'admin\category#update',
    '/admin/category/delete' => 'admin\category#delete',
    '/elfinder' => 'admin\elfinder#index',
    '/elfinder/connector' => 'admin\elfinder#connector',
    '/elfinder/connector/gallery' => 'admin\elfinder#connectorgallery',
    '/elfinder/gallery' => 'admin\elfinder#gallery',

    '/admin/program' => 'admin\program#index',
    '/admin/program/create' => 'admin\program#create',
    '/admin/program/update' => 'admin\program#update',
    '/admin/program/delete' => 'admin\program#delete',
    '/admin/program/course' => 'admin\program#course',
    '/admin/program/course-add' => 'admin\program#courseAdd',

    '/admin/idol' => 'admin\idol#index',
    '/admin/idol/create' => 'admin\idol#create',
    '/admin/idol/update' => 'admin\idol#update',
    '/admin/idol/delete' => 'admin\idol#delete',

    '/admin/introduce' => 'admin\introduce#index',
    '/admin/introduce/create' => 'admin\introduce#create',
    '/admin/introduce/update' => 'admin\introduce#update',
    '/admin/introduce/delete' => 'admin\introduce#delete',

    '/admin/register' => 'admin\register#index',
    '/admin/register/create' => 'admin\register#create',
    '/admin/register/update' => 'admin\register#update',
    '/admin/register/delete' => 'admin\register#delete',

    '/admin/setting' => 'admin\index#setting',
    '/admin/contact' => 'admin\contact#index',

    '/admin/report' => 'admin\report#index',
    '/admin/report/excel' => 'admin\report#excel',
    '/admin/menu' => 'admin\menu#index',

    '/admin/banner' => 'admin\banner#index',
    '/admin/banner/update' => 'admin\banner#update',

    '/admin/slider' => 'admin\slider#index',
    '/admin/slider/create' => 'admin\slider#create',
    '/admin/slider/update' => 'admin\slider#update',
    '/admin/slider/delete' => 'admin\slider#delete',

    '/admin/user' => 'admin\user#index',
    '/admin/user/create' => 'admin\user#create',
    '/admin/user/update' => 'admin\user#update',
    '/admin/user/delete' => 'admin\user#delete',
    '/admin/user/profile' => 'admin\user#profile',

    '/admin/login' => 'admin\index#login',
    '/admin/logout' => 'admin\index#logout',
);
