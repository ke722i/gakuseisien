// 店舗申請フォームの店舗名オートコンプリート。
// 入力された店舗名でGoogle Placesを検索し、候補を選ぶと
// 住所・ジャンル・営業時間・距離・公式URLをまとめて入力する。

const nameInput = document.getElementById('name');
const suggestionList = document.getElementById('name-suggestions');

// 案内文が無い（＝APIキー未設定）画面では何もしない
if (nameInput && suggestionList) {
    const searchUrl = suggestionList.dataset.searchUrl;

    const fields = {
        address: document.getElementById('address'),
        genre: document.getElementById('genre'),
        business_hours: document.getElementById('business_hours'),
        distance: document.getElementById('distance'),
        official_url: document.getElementById('official_url'),
    };

    let debounceTimer = null;
    let latestRequestId = 0;

    const hideSuggestions = () => {
        suggestionList.hidden = true;
        suggestionList.innerHTML = '';
    };

    /** 候補を選んだときに、空欄の項目だけでなく既存の値も含めて上書きする */
    const applyCandidate = (candidate) => {
        nameInput.value = candidate.name || nameInput.value;

        if (candidate.address) {
            fields.address.value = candidate.address;
        }

        if (candidate.business_hours) {
            fields.business_hours.value = candidate.business_hours;
        }

        if (candidate.official_url) {
            fields.official_url.value = candidate.official_url;
        }

        // 学校の座標が未設定のときは distance が null で返るため触らない
        if (candidate.distance !== null && candidate.distance !== undefined) {
            fields.distance.value = candidate.distance;
        }

        // ジャンルは選択肢に存在するときだけ反映する
        if (candidate.genre) {
            const hasOption = Array.from(fields.genre.options).some(
                (option) => option.value === candidate.genre
            );

            if (hasOption) {
                fields.genre.value = candidate.genre;
            }
        }

        hideSuggestions();
    };

    const renderSuggestions = (candidates) => {
        if (candidates.length === 0) {
            hideSuggestions();
            return;
        }

        suggestionList.innerHTML = '';

        candidates.forEach((candidate) => {
            const item = document.createElement('li');
            item.className = 'suggest-item';

            const name = document.createElement('span');
            name.className = 'suggest-name';
            name.textContent = candidate.name;

            const address = document.createElement('span');
            address.className = 'suggest-address';
            address.textContent = candidate.address;

            item.append(name, address);
            item.addEventListener('click', () => applyCandidate(candidate));

            suggestionList.appendChild(item);
        });

        suggestionList.hidden = false;
    };

    const fetchCandidates = async (keyword) => {
        // 遅い応答が後から届いて新しい結果を上書きしないよう、最新の要求だけを採用する
        const requestId = ++latestRequestId;

        try {
            const response = await fetch(
                `${searchUrl}?keyword=${encodeURIComponent(keyword)}`,
                { headers: { Accept: 'application/json' } }
            );

            if (!response.ok || requestId !== latestRequestId) {
                return;
            }

            const data = await response.json();

            renderSuggestions(data.candidates ?? []);
        } catch {
            // 検索に失敗しても手入力は続けられるため、候補を閉じるだけにする
            hideSuggestions();
        }
    };

    nameInput.addEventListener('input', () => {
        const keyword = nameInput.value.trim();

        clearTimeout(debounceTimer);

        // 1文字だと候補が絞れず、APIの呼び出し回数だけが増えてしまう
        if (keyword.length < 2) {
            hideSuggestions();
            return;
        }

        // 入力のたびに呼ばず、手が止まってから検索する
        debounceTimer = setTimeout(() => fetchCandidates(keyword), 400);
    });

    // 候補以外の場所をクリックしたら閉じる
    document.addEventListener('click', (event) => {
        if (!suggestionList.contains(event.target) && event.target !== nameInput) {
            hideSuggestions();
        }
    });

    nameInput.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            hideSuggestions();
        }
    });
}
