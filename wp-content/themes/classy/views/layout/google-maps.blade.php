@if(get_field('maps', 'option')['google_maps'])
    <script src="https://maps.googleapis.com/maps/api/js?key={{ get_field('maps', 'option')['google_maps'] }}&language=ru"></script>
    <script>
        var map; // Global declaration of the map
        var markersArray = []; //массив маркеров на карте
        var locations = [];

        // Получаем данные для карты из пхп
        locations[0] = ['Киев, Кловский спуск, 7', 50.438572, 30.538748];

        function initialize_map() {
            var myOptions = {
                zoom: 15,
                scrollwheel: false,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                mapTypeControl: false,
                mapTypeControlOptions: {position: google.maps.ControlPosition.TOP_RIGHT},
                minZoom: 11,
                maxZoom: 18,
                zoomControlOptions: {style: google.maps.ZoomControlStyle.LARGE},
                center: new google.maps.LatLng(locations[0][1], locations[0][2]),

                // How you would like to style the map.
                // This is where you would paste any style found on Snazzy Maps.
                styles: [
                    {
                        "featureType": "water",
                        "elementType": "geometry",
                        "stylers": [
                            {
                                "color": "#e9e9e9"
                            },
                            {
                                "lightness": 17
                            }
                        ]
                    },
                    {
                        "featureType": "landscape",
                        "elementType": "geometry",
                        "stylers": [
                            {
                                "color": "#f5f5f5"
                            },
                            {
                                "lightness": 20
                            }
                        ]
                    },
                    {
                        "featureType": "road.highway",
                        "elementType": "geometry.fill",
                        "stylers": [
                            {
                                "color": "#ffffff"
                            },
                            {
                                "lightness": 17
                            }
                        ]
                    },
                    {
                        "featureType": "road.highway",
                        "elementType": "geometry.stroke",
                        "stylers": [
                            {
                                "color": "#ffffff"
                            },
                            {
                                "lightness": 29
                            },
                            {
                                "weight": 0.2
                            }
                        ]
                    },
                    {
                        "featureType": "road.arterial",
                        "elementType": "geometry",
                        "stylers": [
                            {
                                "color": "#ffffff"
                            },
                            {
                                "lightness": 18
                            }
                        ]
                    },
                    {
                        "featureType": "road.local",
                        "elementType": "geometry",
                        "stylers": [
                            {
                                "color": "#ffffff"
                            },
                            {
                                "lightness": 16
                            }
                        ]
                    },
                    {
                        "featureType": "poi",
                        "elementType": "geometry",
                        "stylers": [
                            {
                                "color": "#f5f5f5"
                            },
                            {
                                "lightness": 21
                            }
                        ]
                    },
                    {
                        "featureType": "poi.park",
                        "elementType": "geometry",
                        "stylers": [
                            {
                                "color": "#dedede"
                            },
                            {
                                "lightness": 21
                            }
                        ]
                    },
                    {
                        "elementType": "labels.text.stroke",
                        "stylers": [
                            {
                                "visibility": "on"
                            },
                            {
                                "color": "#ffffff"
                            },
                            {
                                "lightness": 16
                            }
                        ]
                    },
                    {
                        "elementType": "labels.text.fill",
                        "stylers": [
                            {
                                "saturation": 36
                            },
                            {
                                "color": "#333333"
                            },
                            {
                                "lightness": 40
                            }
                        ]
                    },
                    {
                        "elementType": "labels.icon",
                        "stylers": [
                            {
                                "visibility": "off"
                            }
                        ]
                    },
                    {
                        "featureType": "transit",
                        "elementType": "geometry",
                        "stylers": [
                            {
                                "color": "#f2f2f2"
                            },
                            {
                                "lightness": 19
                            }
                        ]
                    },
                    {
                        "featureType": "administrative",
                        "elementType": "geometry.fill",
                        "stylers": [
                            {
                                "color": "#fefefe"
                            },
                            {
                                "lightness": 20
                            }
                        ]
                    },
                    {
                        "featureType": "administrative",
                        "elementType": "geometry.stroke",
                        "stylers": [
                            {
                                "color": "#fefefe"
                            },
                            {
                                "lightness": 17
                            },
                            {
                                "weight": 1.2
                            }
                        ]
                    }
                ]
            };

            map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
            google.maps.event.addListener(map, "rightclick", function (event) {
                console.info('lat|lng: ' + event.latLng.lat() + ', ' + event.latLng.lng());
            });

            add_markers();
        }

        function add_markers() {
            var bounds = new google.maps.LatLngBounds(); //массив точек

            var marker_icon = {
                url: "/wp-content/themes/classy/dist/img/google-marker.png",
                size: new google.maps.Size(50, 65),
                origin: new google.maps.Point(0, 0),
                anchor: new google.maps.Point(20, 60)
            };

            var marker, i;
            var infowindow = new google.maps.InfoWindow({
                pixelOffset: new google.maps.Size(0, 0)
            });

            for (i = 0; i < locations.length; i++) {
                var marker_position = new google.maps.LatLng(locations[i][1], locations[i][2]);

                marker = new google.maps.Marker({
                    position: marker_position,
                    map: map,
                    icon: marker_icon
                });

                google.maps.event.addListener(marker, 'click', (function(marker, i) {
                    return function() {
                        infowindow.setContent('<div class="infowindow-content">' + locations[i][0] + '<br>Carnegie center</div>');
                        infowindow.open(map, marker);
                    }
                })(marker, i));

                markersArray.push(marker);
                bounds.extend(marker_position); //добавляем позиции маркеров
            }

            // infoWindow css
            google.maps.event.addListener(infowindow, 'domready', function() {
                $('.infowindow-content').css('padding', '6px');
                $('.infowindow-content').css('font-size', '13px');
                $('.infowindow-content').css('text-align', 'center');
                $('.infowindow-content').css('font-family', "'ProximaNova', sans-serif");

                var wrapper = $('.infowindow-content').parent().parent().parent().siblings();
                for (var i = 0; i < wrapper.length; i++) {
                    if($(wrapper[i]).css('z-index') == 'auto') {
                        wrapper.find('div:last-child').css('background-color', 'transparent');
                        wrapper.parent().find('img').hide();
                        $(wrapper[i]).css('border', '2px solid #C90006');
                        $(wrapper[i]).css('color', '#333');
                        $(wrapper[i]).css('font-size', '18px');
                        $(wrapper[i]).css('background-color', '#1d1d1d');
                        $(wrapper[i]).css('width', '100%');
                        $(wrapper[i]).css('height', '100%');
                        wrapper.parent().fadeIn();
                    }
                }
            });

            //уменьшаем зум
            google.maps.event.addListenerOnce(map, 'idle', function () {
                var zoom = map.getZoom();
                if (zoom > 13) zoom = 13;
                map.setZoom(zoom);
            });

        }

        google.maps.event.addDomListener(window, 'load', initialize_map);

    </script>
@endif
