@extends('layouts.main')

@section('content')

    <section class="container py-[50px] px-[15px] mx-auto">
        <div class="grid grid-cols-12 gap-y-[30px] gap-x-0 xl:gap-y-0 xl:gap-x-[30px]">
            <div class="col-span-12 xl:col-span-3 woocommerce-widgets">
                <a href="{{ wc_get_cart_url() }}" class="flex items-center justify-center cursor-pointer font-secondary no-underline font-extrabold italic text-[16px] text-white text-center bg-dml-blue py-[10px] px-[15px] border-solid border-[1px] border-dml-blue rounded-[5px] shadow-md transition-all ease-in-out duration-300 hover:text-dml-blue hover:bg-white hover:border-dml-blue disabled:text-white disabled:bg-dml-blue-200 disabled:border-dml-blue-200 hover:shadow-xl">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" />
                    </svg>

                    {{ _e( 'View your shopping cart' ) }}
                </a>
                @if(function_exists('dynamic_sidebar'))
                    @php(dynamic_sidebar('sidebar-shop'))
                @endif
            </div>
            <div class="col-span-12 xl:col-span-9">
                @php(do_action('woocommerce_before_main_content'))
                <header class="woocommerce-products-header">
                    @if(apply_filters('woocommerce_show_page_title', true))
                        <h1 class="woocommerce-products-header__title page-title">{{ woocommerce_page_title(false) }}</h1>
                    @endif
                    @php(do_action('woocommerce_archive_description'))
                </header>
                @if(woocommerce_product_loop())
                    @php(do_action('woocommerce_before_shop_loop'))

                    {!! woocommerce_product_loop_start(false) !!}

                    @if(wc_get_loop_prop('total'))
                        @while(have_posts())
                            @php(the_post())

                            @php(do_action('woocommerce_shop_loop'))
                            @php(wc_get_template_part('content', 'product'))
                        @endwhile
                    @endif

                    {!! woocommerce_product_loop_end(false) !!}

                    @php(do_action('woocommerce_after_shop_loop'))
                @else
                    @php(do_action('woocommerce_no_products_found'))
                @endif

                @php(do_action('woocommerce_after_main_content'))
            </div>
        </div>


    </section>
@endsection
