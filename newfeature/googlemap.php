
<?php

?>

<script
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCjCUIgUSAPAgD7gAdxZkwbSacEREeC8TU&callback=initMap&v=weekly"
      defer
    ></script>

    <script>

const tourStops = {
    음식점: [
      [{ lat: -35.02058, lng: 138.52303 }, "The Korean Vibe" , "513 Brighton Rd, Brighton SA 5048" ,"https://adelaideinside.com/data/editor/2401/8eb94b7344a02a8e5773374704e519ef_1704760025_1939.png","https://maps.app.goo.gl/swjUL48GYE4tr1Jz8"],
      [{ lat: -34.92984, lng: 138.59404 }, "Seoul Sweetie","270 morphett St, Adelaide SA 5000","https://adelaideinside.com/data/editor/2312/e3a91bfbbc9f9e2578ac4eb86da266f0_1702517477_6954.png" ,"https://maps.app.goo.gl/GGdssvz8aDDq43179"],
      [{ lat: -34.92991, lng: 138.59422 }, "Busan Baby","272 Morphett St, Adelaide SA 5000", "https://adelaideinside.com/data/editor/2312/e3a91bfbbc9f9e2578ac4eb86da266f0_1702517007_7599.png","https://maps.app.goo.gl/Wt3zvJSCGZiBbj6A9"],
      [{ lat: -34.92781, lng: 138.59710 }, "GO-JJI ADELAIDE BBQ","15 Pitt St, Adelaide SA 5000","https://adelaideinside.com/data/editor/2312/e3a91bfbbc9f9e2578ac4eb86da266f0_1702516629_6749.JPG","https://maps.app.goo.gl/G5df3tdk2PijFsgr5"],
      [{ lat: -34.92759, lng: 138.59352 }, "반반 치킨","145 Franklin St, Adelaide SA 5000","https://adelaideinside.com/data/editor/2312/e3a91bfbbc9f9e2578ac4eb86da266f0_1702516245_1096.png","https://maps.app.goo.gl/bCLqwwErqnSSt9oX7"],
      [{ lat: -34.92620, lng: 138.59575 }, "+82 고기","12 Eliza St, Adelaide SA 5000","https://adelaideinside.com/data/editor/2312/e3a91bfbbc9f9e2578ac4eb86da266f0_1702514956_4662.jpg","https://maps.app.goo.gl/Sz1k9yepSdkUJqZc8"],
      [{ lat: -34.92477, lng: 138.60088 }, "+82 포차","shop 3/25 Grenfell St, Adelaide SA 5000","https://adelaideinside.com/data/editor/2312/e3a91bfbbc9f9e2578ac4eb86da266f0_1702514195_8114.png","https://maps.app.goo.gl/g751ywGx5oS7mUmRA"],
      [{ lat: -34.92991, lng: 138.59422 }, "주로흥할","15 Hindley St, Adelaide SA 5000","https://adelaideinside.com/data/editor/2312/e3a91bfbbc9f9e2578ac4eb86da266f0_1702513514_0472.png","https://maps.app.goo.gl/hGYKrdfDhUoKHZEq5"],
      [{ lat: -34.93120, lng: 138.59589 }, "먹방","31 Field St, Adelaide SA 5000","https://adelaideinside.com/data/editor/2312/e3a91bfbbc9f9e2578ac4eb86da266f0_1702512699_6875.png","https://maps.app.goo.gl/PpCLwCJybL9zBttu6"],
      [{ lat: -34.93089, lng: 138.59491 }, "박봉숙 식당","152 Wright St, Adelaide SA 5000","https://adelaideinside.com/data/editor/2312/e3a91bfbbc9f9e2578ac4eb86da266f0_1702512056_3372.png","https://maps.app.goo.gl/8JMcfPabfQebv5Pt8"],
      [{ lat: -34.93423, lng: 138.60611 }, "고여사","449 Pulteney St, Adelaide SA 5000","https://adelaideinside.com/data/editor/2312/9cca288eabfa3249e72ec6d731e7e672_1702511272_6008.png","https://maps.app.goo.gl/EV5TMmQJokWk3oe86"],
      // 추가적인 음식점 tourstops을 여기에 추가
    ],
    정비: [
      [{ lat: -34.90997, lng: 138.68176 }, "Green Crash Repairs","14 Hender Ave, Magill SA 5072","https://adelaideinside.com/data/editor/2312/91cd9edd5d55a5f2ee985364df6a3b81_1702602338_5552.png","https://maps.app.goo.gl/ehLrYSTh1XWHippo9"],
      // 추가적인 정비 tourstops을 여기에 추가
    ],
    미용: [
      [{ lat: -34.92605, lng: 138.60595 }, "Street hair","186a Pulteney St, Adelaide SA 5000","https://adelaideinside.com/data/editor/2401/b6239d4397d0cf0be9c997f25aa4b601_1705886110_2174.png","https://maps.app.goo.gl/43RtHZKjpcir25pp6"],
      [{ lat: -34.92809, lng: 138.51131 }, "DD Lashes","50 Halsey Road Fulham SA, Australia","https://adelaideinside.com/data/editor/2401/b6239d4397d0cf0be9c997f25aa4b601_1705885743_0693.png","https://maps.app.goo.gl/UeZjbN57wPLShT5E9"],
      [{ lat: -34.88816, lng: 138.67468 }, "The Born Beauty","6/145 Montacute Rd, Newton SA 5074","https://adelaideinside.com/data/editor/2312/7eeda3c48a764178bea3003fdff56c24_1702621787_3727.png","https://maps.app.goo.gl/1N3uYwdMu6nPmrqr6"],
      [{ lat: -34.92036, lng: 138.64955 }, "Star Hair Salon","325 The Parade, Beulah Park SA 5067","https://adelaideinside.com/data/editor/2312/7eeda3c48a764178bea3003fdff56c24_1702621530_4618.png","https://maps.app.goo.gl/APeGpqc7QuLYBcPN7"],
      [{ lat: -34.92217, lng: 138.60354 }, "Hair Beaute","Shop/14 Charles St, Adelaide SA 5000","https://adelaideinside.com/data/editor/2312/7eeda3c48a764178bea3003fdff56c24_1702621296_0536.jpeg","https://maps.app.goo.gl/1fAyB9kmJz69oXeYA"],
      [{ lat: -34.92856, lng: 138.59450 }, "Oh Kim's Hair Salon","128A Grote St, Adelaide SA 5000","https://adelaideinside.com/data/editor/2312/7eeda3c48a764178bea3003fdff56c24_1702621176_3691.png","https://maps.app.goo.gl/q7gF7FBtBLMSpnSQ8"],
      [{ lat: -34.92306, lng: 138.60575 }, "KOrean COlor Hairsalon","56 Pulteney St, Adelaide SA 5000","https://adelaideinside.com/data/editor/2312/7eeda3c48a764178bea3003fdff56c24_1702621027_2335.png","https://maps.app.goo.gl/W1aGYtTxeHpnpxFT7"],
      [{ lat: -34.89021, lng: 138.65468 }, "Cozy Hair","Shop 6/474-476 Payneham Rd, Glynde SA 5070","https://adelaideinside.com/data/editor/2312/7eeda3c48a764178bea3003fdff56c24_1702620820_4782.png","https://maps.app.goo.gl/3BAAPSLZXMXEiLiD9"],
      [{ lat: -34.94762, lng: 138.62797 }, "Salon A by Genie","193A Glen Osmond Rd, Frewville SA 5063","https://adelaideinside.com/data/editor/2401/b6239d4397d0cf0be9c997f25aa4b601_1706571484_6158.png","https://maps.app.goo.gl/aGfkucKaRBucUnBZ9"],
      // 추가적인 미용 tourstops을 여기에 추가
    ],
    
    부동산: [
      [{ lat: -35.00159, lng: 138.59293 }, "Otherhome Sharehouse","3 Moore St, Pasadena SA 5042","https://adelaideinside.com/data/editor/2312/95453068bbdb3e279d6deab8d5d8e2d5_1702450536_3551.png","https://maps.app.goo.gl/793BAawdrTaRqgu87"],
      // 추가적인 부동산 tourstops을 여기에 추가
    ],
    정육: [
      [{ lat: -34.88034, lng: 138.68451 }, "yes butcher","2/6 Meredith st. Newton","https://adelaideinside.com/data/editor/2401/c74969568b0da1066b02b045ccf971bc_1704258634_0021.jpg","https://maps.app.goo.gl/cFtFeiKjRipMHU8g6"],
      [{ lat: -34.89270, lng: 138.64912 }, "최가네 정육점","4/418 Payneham Rd, Glynde SA 5070","https://adelaideinside.com/data/editor/2312/95453068bbdb3e279d6deab8d5d8e2d5_1702445435_9297.jpg","https://maps.app.goo.gl/mrEhBVuigLCMiSov5"],
      // 추가적인 정육 tourstops을 여기에 추가
    ],
    한인마트: [
      [{ lat: -34.90722, lng: 138.59519 }, "빌리지마트","T25/67 O'Connell St, North Adelaide SA 5006","https://adelaideinside.com/data/editor/2312/7eeda3c48a764178bea3003fdff56c24_1702525732_1306.jpeg","https://maps.app.goo.gl/43F1VGgTpn7XyCiv6"],
      [{ lat: -34.92841, lng: 138.59668 }, "서울식품","66 Grote St, Adelaide SA 5000","https://adelaideinside.com/data/editor/2312/7eeda3c48a764178bea3003fdff56c24_1702525593_3085.jpeg","https://maps.app.goo.gl/quhVnctaxP2zSASf9"],
      [{ lat: -34.92886, lng: 138.59693 }, "코리아나마트","Market Plaza, Shop 2-5, 61/63 Grote St, Adelaide SA 5000","https://adelaideinside.com/data/editor/2312/7eeda3c48a764178bea3003fdff56c24_1702525441_323.jpeg","https://maps.app.goo.gl/mcVd45zFkeXh6r5e6"],
      [{ lat: -34.90255, lng: 138.65708 }, "패밀리마트","161 Glynburn Rd, Firle SA 5070","https://adelaideinside.com/data/editor/2312/7eeda3c48a764178bea3003fdff56c24_1702525241_3454.png","https://maps.app.goo.gl/KEA3fvGs5JGJ3JSG6"],
      [{ lat: -34.92077, lng: 138.64478 }, "해피마트","5068/298 The Parade, Kensington SA 5068","https://adelaideinside.com/data/editor/2312/7eeda3c48a764178bea3003fdff56c24_1702525073_4672.png","https://maps.app.goo.gl/BLrmGJCZp7Jgz9bn8"],
      [{ lat: -34.88991, lng: 138.65546 }, "Lucky Mart","482A Payneham Rd, Glynde SA 5070","https://adelaideinside.com/data/editor/2312/7eeda3c48a764178bea3003fdff56c24_1702524755_5733.png","https://maps.app.goo.gl/TyJersvs4HyLA2339"],
      [{ lat: -34.90063, lng: 138.63490 }, "Together Mart","Shop 2-3/291 Payneham Rd, Royston Park SA","https://adelaideinside.com/data/editor/2312/7eeda3c48a764178bea3003fdff56c24_1702524408_0564.jpeg","https://maps.app.goo.gl/AQ6sYMc1JtgkcB6P8"],
      // 추가적인 정육 tourstops을 여기에 추가
    ],
    카페: [
      [{ lat: -34.92356, lng: 138.59781 }, "Waffle & Coffee","2/20-24 Leigh St, Adelaide SA 5000","https://adelaideinside.com/data/editor/2312/7eeda3c48a764178bea3003fdff56c24_1702622823_3775.jpeg","https://maps.app.goo.gl/RiNQSnYpL2Y7cUzNA"],
      [{ lat: -34.93224, lng: 138.60337 }, "Seoul Sisters","84 Halifax St, Adelaide SA 5000","https://adelaideinside.com/data/editor/2312/7eeda3c48a764178bea3003fdff56c24_1702607639_5521.png","https://maps.app.goo.gl/HXv2gzA1SDuxSH1V9"],
      [{ lat: -34.92395, lng: 138.55839 }, "Latte Studio","208 Henley Beach Rd, Torrensville SA 5031","https://adelaideinside.com/data/editor/2312/62f7992637f97312009c21b1875876a0_1702527995_7108.jpg","https://maps.app.goo.gl/7xdYSuC12FmyVxGe9"],
      // 추가적인 정육 tourstops을 여기에 추가
    ],
    회계: [
      [{ lat: -34.90566, lng: 138.60926 }, "SKS Accountant", "Shop 59/53-55 Melbourne St, North Adelaide SA 5006", "https://adelaideinside.com/data/editor/2401/38f95a2b5c6a669fc8ea19795edd6621_1706588587_4673.png", "https://maps.app.goo.gl/6ATbxTEKCtjan9Vj9" ],
      // 추가적인 정육 tourstops을 여기에 추가
    ],
    의료: [
      [{ lat: -34.93101, lng: 138.59610 }, "Harmony Aesthetic Clinic","1st Floor Tenancy 2/22-30 Field St, Adelaide CBD","https://adelaideinside.com/data/editor/2401/c74969568b0da1066b02b045ccf971bc_1704261086_3574.png","https://maps.app.goo.gl/ukKuTUGkMtqyi2pG9"],
      [{ lat: -34.83199, lng: 138.69123 }, "Bupa Dental Tea Tree Plaza","976 North East Road, Modbury SA 5092","https://adelaideinside.com/data/editor/2312/4fc643dcb48ffe00987b6d9eefb182d1_1703297577_3009.jpg","https://maps.app.goo.gl/DNUvNrHssayeN2us9"],
      [{ lat: -34.90884, lng: 138.59580 }, "Anew Smile Implant Centre","151-159 Ward St, North Adelaide, SA 5006","https://adelaideinside.com/data/editor/2312/493eb846c1d08b25da4a70a96acf1b0c_1703222890_2085.png","https://maps.app.goo.gl/cZRi6gSYQNRauyXQ9"],
      [{ lat: -35.06815, lng: 138.86458 }, "Mount Barker Dentists","15 Victoria Crescent, Mt Barker SA 5251","https://adelaideinside.com/data/editor/2312/493eb846c1d08b25da4a70a96acf1b0c_1703222511_6712.jpg","https://maps.app.goo.gl/sMvN5e6RDAwMpEhg7"],
      [{ lat: -34.92059, lng: 138.63777 }, "Primary Dental Norwood","201–203 The Parade Norwood SA 5067","https://adelaideinside.com/data/editor/2312/493eb846c1d08b25da4a70a96acf1b0c_1703222137_0339.jpg","https://maps.app.goo.gl/9TSHyZtHWsv8SDpE9"],
      [{ lat: -34.80550, lng: 138.61593 }, "Adelaide Disability Medical Services","Shop1, 6-12 Capital St, Mawson Lakes, SA 5095","https://adelaideinside.com/data/editor/2312/493eb846c1d08b25da4a70a96acf1b0c_1703221765_2906.png","https://maps.app.goo.gl/LZqcvtwgcLUcJCDA9"],
      [{ lat: -34.97477, lng: 138.60948 }, "Pro Health Care Mitcham","105 Belair Rd, Torrens Park SA 5062","https://adelaideinside.com/data/editor/2312/493eb846c1d08b25da4a70a96acf1b0c_1703221384_0101.jpg","https://maps.app.goo.gl/Y2KfVX3kND8sgebp9"],
      [{ lat: -34.85181, lng: 138.50824 }, "Trinity Medical Centre - Port Adelaide","28 College St, Port Adelaide SA 5015","https://adelaideinside.com/data/editor/2312/493eb846c1d08b25da4a70a96acf1b0c_1703220868_5386.jpeg","https://maps.app.goo.gl/6ZfjKU9rZeCyViVT9"],
      [{ lat: -34.92708, lng: 138.63139 }, "Pro Health Care Norwood","93 Kensington Road, Norwood SA 5067","https://adelaideinside.com/data/editor/2312/493eb846c1d08b25da4a70a96acf1b0c_1703218742_0067.png","https://maps.app.goo.gl/ug3ZC5K5GZyPvo4s5"],
      [{ lat: -34.93980, lng: 138.63680 }, "East Adelaide Dental Studio","1 Allinga Ave, Glenside SA 5065","https://adelaideinside.com/data/editor/2312/7eeda3c48a764178bea3003fdff56c24_1702622525_8343.png","https://maps.app.goo.gl/Z3ewn4SvMThrNKSW7"],
      [{ lat: -34.92380, lng: 138.60057 }, "JYL Optical Outlet","29 James Pl, Adelaide SA 5000","https://adelaideinside.com/data/editor/2312/7eeda3c48a764178bea3003fdff56c24_1702622277_1957.png","https://maps.app.goo.gl/CfMdqeHq89i3aQGPA"],
      [{ lat: -34.90022, lng: 138.65673 }, "Glynde Veterinary Surgery","125 Glynburn Rd, Glynde SA 5070","https://adelaideinside.com/data/editor/2401/b6239d4397d0cf0be9c997f25aa4b601_1706492987_5318.png","https://maps.app.goo.gl/t8MsrycBqRjqrq3Z9"],
      
      // 추가적인 정육 tourstops을 여기에 추가
    ],
    기타: [
      [{ lat: -34.92255, lng: 138.59974 }, "Study SA 유학원","Level 3, 50 King William Street, Adelaide, SA 5000, Australia","https://adelaideinside.com/data/editor/2401/c74969568b0da1066b02b045ccf971bc_1704258275_0216.png","https://maps.app.goo.gl/ufcHMezs2Ps4xQtdA"],
      [{ lat: -34.92433, lng: 138.60087 }, "정에듀 Jung Education Australia","25 grenfell st. Adelaide SA 5000","https://adelaideinside.com/data/editor/2401/a3a06945bdb0b5a399e7e9dbd1491dd4_1704258020_8214.jpg","https://maps.app.goo.gl/ycWthuaiyM2QFpjG8"],
      // 추가적인 정육 tourstops을 여기에 추가
    ],
  };


  function initMap() {
  

    const map = new google.maps.Map(document.getElementById("map"), {
    zoom: 11.5,
    center: { lat: -34.92843, lng: 138.60002 },
    });

  // Create an info window to share between markers.
  const infoWindow = new google.maps.InfoWindow( {maxWidth: 190, maxHeight:250} );

  // 선택한 카테고리에 해당하는 tourstop 정보 가져오기
  const selectedTourStops = tourStops["음식점"];

  console.log(selectedTourStops);

  if (selectedTourStops) {
    // 선택한 카테고리에 대한 모든 마커 생성
    for (let i = 0; i < selectedTourStops.length; i++) {
      const [position, title, address, imglink, link] = selectedTourStops[i];
      const marker = new google.maps.Marker({
        position,
        map,
        title: `${i + 1}. ${title}`,
        label: `${i + 1}`,
        optimized: false,
      });


      const contentString = `<div class="card" style="height:17rem; border-radius: 5px;" >
                                    <img class="card-img-top " style="height:7rem;" src="${imglink}" alt="Card image cap">
                                    <div class="card-body">
                                        <h6 class="card-title" style= font-family:  'arial', sans-serif !important;>${marker.title}</h6>
                                        <p class="card-text" style= font-family:  'arial', sans-serif !important;>${address}</p>
                                    </div>
                                    <a href="${link}" target="_blank" class="btn text-white" style="background-color:#ffc51b; font-family:'arial', sans-serif !important; font-weight:bold;" >구글맵 바로가기</a>
                                </div>`



      // 마커에 클릭 이벤트 핸들러 추가
      marker.addListener("click", () => {
        infoWindow.close();
        infoWindow.setContent(contentString);
        infoWindow.open({
            anchor: marker,
            map,
            shouldFocus: false,
        });
      });
    }
  }

  }

window.initMap = initMap;



function updateSelection(category) {
        // 선택된 항목을 div에 표시
        document.getElementById('selectedText').innerText = category;
    }



function findingStores(category) {
  const map = new google.maps.Map(document.getElementById("map"), {
    zoom: 11.5,
    center: { lat: -34.92843, lng: 138.60002 },
  });
  
  // Create an info window to share between markers.
  const infoWindow = new google.maps.InfoWindow( {maxWidth: 210, maxHeight:250} );
  

  // 선택한 카테고리에 해당하는 tourstop 정보 가져오기
  const selectedTourStops = tourStops[category];

  if (selectedTourStops) {
    // 선택한 카테고리에 대한 모든 마커 생성
    for (let i = 0; i < selectedTourStops.length; i++) {
      const [position, title, address, imglink, link] = selectedTourStops[i];
      const marker = new google.maps.Marker({
        position,
        map,
        title: `${i + 1}. ${title}`,
        label: `${i + 1}`,
        optimized: false,
      });


      const contentString = `<div class="card" style="height:17rem; border-radius: 5px;" >
                                    <img class="card-img-top " style="height:7rem;" src="${imglink}" alt="Card image cap">
                                    <div class="card-body">
                                        <h6 class="card-title" style= font-family:  'arial', sans-serif !important;>${marker.title}</h6>
                                        <p class="card-text" style= font-family:  'arial', sans-serif !important;>${address}</p>
                                    </div>
                                    <a href="${link}" target="_blank" class="btn text-white" style="background-color:#ffc51b; font-family:'arial', sans-serif !important; font-weight:bold;" >구글맵 바로가기</a>
                                </div>`



      // 마커에 클릭 이벤트 핸들러 추가
      marker.addListener("click", () => {
        infoWindow.close();
        infoWindow.setContent(contentString);
        infoWindow.open({
            anchor: marker,
            map,
            shouldFocus: false,
        });
      });
    }
  }
}



    </script>
        



        <!-- 지도가 표기될 div -->
        
    <div id="map" style=" height:500px; margin-top:15px; border-radius: 10px;"></div>

    <div class="dropdown" style="margin-top : 20px; display: flex; justify-content: center;" >
            <button class="text-white dropdown-toggle" style="background-color:#ffc51b;
  border: 1px solid transparent;
  border-radius: 10px;
  box-shadow: rgba(255, 255, 255, .4) 0 1px 0 0 inset;
  box-sizing: border-box;
  color: #fff;
  cursor: pointer;
  display: inline-block;
  font-family:  'arial', sans-serif !important;
  font-size: 16px;
  font-weight: bold;
  line-height: 1.15385;
  margin: 0;
  outline: none;
  padding: 8px .8em;
  position: relative;
  text-align: center;
  text-decoration: none;
  user-select: none;
  -webkit-user-select: none;
  touch-action: manipulation;
  vertical-align: baseline;
  white-space: nowrap;" type="button" id="dropdownMenuButton1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <div id="selectedItem">
            한눈에 보기 : <span id="selectedText">음식점</span>
           </div>
            </button>
        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
            <a class="dropdown-item" href="#" onclick="dropdownClick('음식점')">음식점</a>
            <a class="dropdown-item" href="#" onclick="dropdownClick('미용')">미용</a>
            <a class="dropdown-item" href="#" onclick="dropdownClick('부동산')">부동산</a>
            <a class="dropdown-item" href="#" onclick="dropdownClick('정비')">정비</a>
            <a class="dropdown-item" href="#" onclick="dropdownClick('정육')">정육</a>
            <a class="dropdown-item" href="#" onclick="dropdownClick('한인마트')">한인마트</a>
            <a class="dropdown-item" href="#" onclick="dropdownClick('카페')">카페</a>
            <a class="dropdown-item" href="#" onclick="dropdownClick('회계')">회계</a>
            <a class="dropdown-item" href="#" onclick="dropdownClick('의료')">의료</a>
            <a class="dropdown-item" href="#" onclick="dropdownClick('기타')">기타</a>
        </div>
    </div>
    <script>

    function dropdownClick(category) {
        console.log(category);
        // 선택된 항목을 div에 표시
        event.preventDefault(); // 기본 동작 방지 --> 구글맵 드랍다운 메뉴 선택시 새로고침 되어 맨 위로 가는 것을 방지
        updateSelection(category);
        findingStores(category);     
    }

</script>