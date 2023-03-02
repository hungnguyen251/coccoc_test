<?php
require_once dirname(__DIR__, 1) . "/api/advert.php";

$advert = new Advert();
$page = $_GET['page'] ?? 1;
$totalPage = ceil($advert->getCount()->total / 10);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.3/jquery.min.js" integrity="sha512-STof4xm1wgkfm7heWqFJVn58Hm3EtS31XFaagaa8VMReCXAkQnJZ+jEy8PCC/iT18dFy95WcExNHFTqLyp72eQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <title>Show</title>
</head>
<body>
    <div class="container">
        <div class="row notification">
        </div>
        <div class="row">
            <div class="btn">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">
                    Add Ads
                </button>
            </div>
        </div>
        <div class="test">
            <table class="table table-bordered border-primary">
                <thead>
                    <tr>
                        <th scope="col">Image</th>
                        <th scope="col">Tiltle</th>
                        <th scope="col">Price</th>
                    </tr>
                </thead>
                <tbody class="table-striped show-list">

                </tbody>
            </table>
        </div>

        <div class="card-footer clearfix">
            <ul class="pagination pagination-sm m-0 float-right">
                <?php for ($j = 0; $j < $totalPage; $j++) {?>
                <li class="page-item <?php if ($page == ($j + 1)) {
                    echo "active";
                } ?>"><a class="page-link" href="?page=<?php echo $j + 1; ?>"><?php echo $j + 1; ?></a>
                </li>
                <?php } ?>
          </ul>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal" id="myModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Create Ads</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                
                <!-- Modal body -->
                <form id="form" method="post">
                    <div class="modal-body">
                        <div class="form-group d-flex">
                            <div class="form-group col-sm-12 px-0">
                                <label for="">Title (*)</label>
                                <input type="text" class="form-control title" id="title" value="" name="title" placeholder="Request to enter title">
                            </div>
                        </div>

                        <div class="form-group d-flex">
                            <div class="form-group col-sm-12 px-0">
                                <label for="">Price (*)</label>
                                <input type="text" class="form-control price" id="price" name="price" placeholder="Request to enter price">
                            </div>
                        </div>

                        <div class="form-group d-flex">
                            <div class="form-group col-sm-12 px-0">
                                <label for="">Image Link 1 (*)</label>
                                <input type="text" class="form-control mb-2" id="img-1" name="image" placeholder="Request to enter image 1">
                                <input type="text" class="form-control mb-2" id="des-1" name="description" placeholder="Description image 1">

                                <label class="mt-3" for="">Image Link 2</label>
                                <input type="text" class="form-control mb-2" id="img-2" name="image" placeholder="Request to enter image 2">
                                <input type="text" class="form-control mb-2" id="des-2" name="description" placeholder="Description image 2">

                                <label class="mt-3" for="">Image Link 3</label>
                                <input type="text" class="form-control mb-2" id="img-3" name="image" placeholder="Request to enter image 3">
                                <input type="text" class="form-control mb-2" id="des-3" name="description" placeholder="Description image 3">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" name="submit" class="btn btn-danger" data-dismiss="modal" onclick="myFunction()" id="create-ads">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

<script>
    var url =  window.location.protocol + "//" + window.location.host + "/advert/get-list" + location.search;

    async function getDataListAds() {        
        try {
            let res = await fetch(url);
            return await res.json();
        } catch (error) {
            console.log(error);
        }
    }

    async function renderListAds() {
        let data = await getDataListAds();
        let html = '';
        
        if(data.code == 200) {
            let list = data.data;

            for(i = 0; i < list.length; i++) {
                let htmlSegment =   `<tr>
                                        <td>`+list[i].link+`</td>
                                        <td>`+list[i].title+`</td>
                                        <td>`+list[i].price+`</td>
                                    </tr>`;

                html += htmlSegment;
            }
            $('.show-list').html(html);
        }
    }

    renderListAds();

    function myFunction() {
        $("#title").prop('required',true);
        $("#price").prop('required',true);
        $("#img-1").prop('required',true);

        let createAdsUrl =  window.location.protocol + "//" + window.location.host + "/advert/create";
        let title = $("#title").val();
        let price = $("#price").val();
        let link = [];
        let description = [];

        if ('' != $("#img-1").val()) {
            link.push($("#img-1").val());
            description.push($("#des-1").val());
        } 

        if ('' != $("#img-2").val()) {
            link.push($("#img-2").val());
            description.push($("#des-2").val());
        }

        if ('' != $("#img-3").val()) {
            link.push($("#img-3").val());
            description.push($("#des-3").val());
        }

        let dataBody = JSON.stringify({"title" : title, "price" : price, "link" : link, "description" : description});
        console.log(dataBody);
        $.ajax({
            type: "POST",
            url: createAdsUrl,
            dataType: "json",
            data: dataBody,
            processData: false,
            contentType: false,
            success: function (response) {
                console.log(response);
                if (200 == response.code) {
                    alert('Add successful');
                    window.location.reload();

                } else {
                    alert('Add failed');
                    window.location.reload();
                }
            }
        });
    }
</script>