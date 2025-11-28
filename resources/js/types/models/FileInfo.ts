/**
 * FileInfo関連の型定義
 * FileInfo-related type definitions
 */
type BaseObj = {
  /** 導入日 */
  intro_date?: number;
  /** 引退日 */
  retire_date?: number;
  /** データフォーマットバージョン */
  version?: number;
};

/**
 * 車両データ型
 * Vehicle data type
 */
export interface VehicleData extends BaseObj {
  /** 輸送容量 */
  capacity?: number;
  /** 購入価格 (単位: 100cr) */
  price?: number;
  /** 最高速度 (km/h) */
  topspeed?: number;
  /** 重量 (ton) */
  weight?: number;
  /** 出力 (kW) */
  power?: number;
  /** 運用コスト (単位: 0.01cr/km) */
  running_cost?: number;
  /** 維持費 (単位: 0.01cr/月) */
  maintenance?: number;
  /** 乗降時間 (ms) */
  loading_time?: number;
  /** 軸重 (ton) */
  axle_load?: number;
  /** 導入日 */
  intro_date?: number;
  /** 引退日 */
  retire_date?: number;
  /** ギア比 (64が標準) */
  gear?: number;
  /** 道路タイプID */
  waytype?: number;
  /** 道路タイプ名 (例: "road", "track", "water") */
  waytype_str?: string;
  /** サウンドID */
  sound?: number;
  /** エンジンタイプID (0-5) */
  engine_type?: number;
  /** エンジンタイプ名 (例: "Steam", "Diesel", "Electric") */
  engine_type_str?: string;
  /** 車両長 (タイル数の8倍) */
  len?: number;
  /** 先頭車両数 */
  leader_count?: number;
  /** 付随車両数 */
  trailer_count?: number;
  /** 貨物画像タイプ */
  freight_image_type?: number;
  /** 輸送品目名 (例: "Passagiere", "Post") */
  freight_type?: string;
}

/**
 * 道路データ型
 * Way data type
 */
export interface WayData extends BaseObj {
  /** 建設価格 (単位: 100cr/タイル) */
  price?: number;
  /** 維持費 (単位: 0.01cr/月/タイル) */
  maintenance?: number;
  /** 制限速度 (km/h) */
  topspeed?: number;
  /** 最大重量制限 (ton) */
  max_weight?: number;
  /** 軸重制限 (ton, 9999 = 無制限) */
  axle_load?: number;
  /** 道路タイプID (0=道路, 1=線路, 2=単軌鉄道, etc) */
  waytype?: number;
  /** 道路タイプ名 (例: "Road", "Track", "Monorail") */
  waytype_str?: string;
  /** システムタイプID (0=平地, 1=高架, 7=地下, etc) */
  styp?: number;
  /** システムタイプ名 (例: "Flat", "Elevated", "Tram") */
  styp_str?: string;
  /** オブジェクトとして描画するか */
  draw_as_obj?: boolean | number;
  /** 季節画像数 */
  number_of_seasons?: number;
  /** 前面画像の有無 (version > 4) */
  front_images?: boolean;
}

/**
 * Way-object data type (overhead lines, catenary)
 */
export interface WayObjectData extends BaseObj {
  /** 建設費用 (単位: 1/100 credits per tile) */
  price?: number;
  /** 月間維持費 (単位: 0.01cr/月) */
  maintenance?: number;
  /** 最高速度制限 (km/h, 0 = 無制限) */
  topspeed?: number;
  /** 配置可能なwaytype (数値) */
  waytype?: number;
  /** 配置可能なwaytype (文字列) */
  waytype_str?: string;
  /** オブジェクト自身のwaytype (数値) */
  own_waytype?: number;
  /** オブジェクト自身のwaytype (文字列) */
  own_waytype_str?: string;
}

/**
 * Bridge data
 * 橋梁データ型
 */
export interface BridgeData extends BaseObj {
  /** データフォーマットバージョン (0-10) */
  version?: number;
  /** 配置可能なwaytype (数値) */
  waytype?: number;
  /** 配置可能なwaytype (文字列) */
  waytype_str?: string;
  /** 最高速度制限 (km/h, 0 = 無制限) */
  topspeed?: number;
  /** 建設費用 (単位: 1/100 credits per tile) */
  price?: number;
  /** 月間維持費 (単位: 0.01cr/月) */
  maintenance?: number;
  /** 軸重制限 (トン, 9999 = 無制限) */
  axle_load?: number;
  /** 支柱間隔 (タイル, 0 = 支柱なし) */
  pillars_every?: number;
  /** 斜面での最下部支柱を省略 */
  pillars_asymmetric?: boolean;
  /** 最大橋長 (タイル, 0 = 無制限) */
  max_length?: number;
  /** 最大高度 (タイル, 0 = 無制限) */
  max_height?: number;
  /** 季節グラフィック数 (0 = なし, 1 = 雪あり) */
  number_of_seasons?: number;
}

/**
 * Tunnel data
 * トンネルデータ型
 */
export interface TunnelData extends BaseObj {
  /** データフォーマットバージョン (1-6) */
  version?: number;
  /** 配置可能なwaytype (数値) */
  waytype?: number;
  /** 配置可能なwaytype (文字列) */
  waytype_str?: string;
  /** 最高速度制限 (km/h, 0 = 無制限) */
  topspeed?: number;
  /** 建設費用 (単位: 1/100 credits per tile) */
  price?: number;
  /** 月間維持費 (単位: 0.01cr/月) */
  maintenance?: number;
  /** 軸重制限 (トン, 9999 = 無制限) */
  axle_load?: number;
  /** 季節グラフィック数 (0 = なし, 1 = 雪あり) */
  number_of_seasons?: number;
  /** 地下の線路/道路を表示 */
  has_way?: boolean;
  /** 広い入口バリエーション (左/右/中央) */
  broad_portals?: boolean;
}

/**
 * Crossing (Level crossing) data
 * 踏切データ型
 */
export interface CrossingData extends BaseObj {
  /** データフォーマットバージョン (1-2) */
  version: number;
  /** 道路タイプ1 (数値) */
  waytype1: number;
  /** 道路タイプ1 (文字列) */
  waytype1_str: string;
  /** 道路タイプ2 (数値) */
  waytype2: number;
  /** 道路タイプ2 (文字列) */
  waytype2_str: string;
  /** 最高速度1 (km/h) */
  topspeed1: number;
  /** 最高速度2 (km/h) */
  topspeed2: number;
  /** 開放アニメーション時間 (ms) */
  open_animation_time: number;
  /** 閉鎖アニメーション時間 (ms) */
  closed_animation_time: number;
  /** サウンドID (-2 = 埋め込みファイル名あり) */
  sound: number;
  /** 埋め込みサウンドファイル名 (sound=-2の場合のみ) */
  sound_filename?: string;
}

/**
 * Citycar (Private city car) data
 * 市内自動車データ型
 */
export interface CitycarData extends BaseObj {
  /** 出現確率（重み） */
  distribution_weight: number;
  /** 最高速度 (km/h) */
  topspeed: number;
}

/**
 * Pedestrian（歩行者）データ
 */
export interface PedestrianData extends BaseObj {
  /** 出現確率（重み） / Distribution weight (spawn probability) */
  distribution_weight: number;
  /** 1フレームあたりの歩数（歩行速度） / Steps per frame (walking speed) */
  steps_per_frame?: number;
  /** 描画オフセット / Drawing offset */
  offset?: number;
}

/**
 * Tree（木）データ
 */
export interface TreeData extends BaseObj {
  /** 許可される気候（ビットマスク） / Allowed climates (bitmask) */
  allowed_climates: number;
  /** 許可される気候（文字列） / Allowed climates (string) */
  allowed_climates_str: string;
  /** 出現確率（重み） / Distribution weight (spawn probability) */
  distribution_weight: number;
  /** 季節数（0=旧形式） / Number of seasons (0=old format) */
  number_of_seasons: number;
}

/**
 * Groundobj（地上オブジェクト）データ
 */
export interface GroundobjData extends BaseObj {
  /** 許可される気候（ビットマスク） / Allowed climates (bitmask) */
  allowed_climates: number;
  /** 許可される気候（文字列） / Allowed climates (string) */
  allowed_climates_str: string;
  /** 出現確率（重み） / Distribution weight (spawn probability) */
  distribution_weight: number;
  /** 季節数 / Number of seasons */
  number_of_seasons: number;
  /** 上に木を生やせるか / Can build trees on top */
  trees_on_top: boolean;
  /** 速度（0=静止、>0=移動） / Speed (0=static, >0=moving) */
  speed: number;
  /** 移動可能地形タイプ / Waytype for movement */
  waytype: number;
  /** 移動可能地形タイプ（文字列） / Waytype (string) */
  waytype_str: string;
  /** 撤去コスト / Removal cost */
  price: number;
}

/**
 * Ground（地形テクスチャ）データ
 */
export interface GroundData extends BaseObj {
  /** データフィールドの有無（常にfalse） / Has data fields (always false) */
  has_data: boolean;
}

/**
 * Sound（効果音）データ
 */
export interface SoundData extends BaseObj {
  /** サウンドID / Sound ID */
  sound_id: number;
  /** ファイル名（v2のみ） / Filename (v2 only) */
  filename?: string;
}

/**
 * Skin（スキン/UI画像）データ
 */
export interface SkinData extends BaseObj {
  /** データフィールドの有無（常にfalse） / Has data fields (always false) */
  has_data: boolean;
  /** オブジェクトサブタイプ ('skin' または 'smoke') / Object subtype */
  object_subtype: string;
}

/**
 * Factory (Industrial building) data
 * 工場データ型
 */
export interface FactoryData extends BaseObj {
  /** 配置タイプ (数値: 0=Land, 1=Water, 2=City) */
  placement: number;
  /** 配置タイプ (文字列) */
  placement_str: string;
  /** 生産性 */
  productivity: number;
  /** 供給範囲 */
  range: number;
  /** 出現確率（重み） */
  distribution_weight: number;
  /** 表示色 */
  color: number;
  /** フィールド数 (v2+) */
  fields?: number;
  /** 供給元数 */
  supplier_count: number;
  /** 生産物数 */
  product_count: number;
  /** 乗客レベル */
  pax_level: number;
  /** 拡張確率 (v2+) */
  expand_probability?: number;
  /** 拡張最小値 (v2+) */
  expand_minimum?: number;
  /** 拡張範囲 (v2+) */
  expand_range?: number;
  /** 拡張回数 (v2+) */
  expand_times?: number;
  /** 電力ブースト (v3+) */
  electric_boost?: number;
  /** 乗客ブースト (v3+) */
  pax_boost?: number;
  /** 郵便ブースト (v3+) */
  mail_boost?: number;
  /** 電力需要 (v3+) */
  electric_demand?: number;
  /** 乗客需要 (v3+) */
  pax_demand?: number;
  /** 郵便需要 (v3+) */
  mail_demand?: number;
  /** サウンド間隔 (v4+) */
  sound_interval?: number;
  /** サウンドID (v4+) */
  sound_id?: number;
  /** 煙回転数 (v5+) */
  smokerotations?: number;
  /** 煙上昇量 (v5+) */
  smokeuplift?: number;
  /** 煙寿命 (v5+) */
  smokelifetime?: number;
}

/**
 * Good/Freight data
 * 貨物データ型
 */
export interface GoodData extends BaseObj {
  /** データフォーマットバージョン (0-4) */
  version: number;
  /** 基本価値 (経済価値) */
  base_value: number;
  /** カテゴリID (0=特殊貨物, 1=小口貨物, 2=バルク貨物, 3=長尺貨物, 4=液体貨物, 5=冷蔵貨物, 6=旅客, 7=郵便, 8=なし) */
  catg: number;
  /** カテゴリ名 (例: "passengers", "mail", "piece_goods") */
  catg_str: string;
  /** 速度ボーナス (パーセント, 速い輸送で収益増) */
  speed_bonus: number;
  /** 単位重量 (KG/単位) */
  weight_per_unit: number;
  /** 表示色インデックス (0-255) */
  color: number;
}

/**
 * Roadsign/Signal data
 * 道路標識/信号データ型
 */
export interface SignData extends BaseObj {
  /** 配置可能なwaytype (数値) */
  waytype?: number;
  /** 配置可能なwaytype (文字列) */
  waytype_str?: string;
  /** 最低速度制限 (km/h, 0 = 制限なし) */
  min_speed?: number;
  /** 建設費用 (単位: 1/100 credits) */
  price?: number;
  /** 月間維持費 (単位: 0.01cr/月) */
  maintenance?: number;
  /** フラグ (ビットフィールド) */
  flags?: number;
  /** 左オフセット (画像配置調整) */
  offset_left?: number;
  /** 一方通行標識 */
  is_one_way?: boolean;
  /** ルート選択標識 */
  is_choose_sign?: boolean;
  /** 専用道路 */
  is_private_way?: boolean;
  /** 信号機 */
  is_signal?: boolean;
  /** 予告信号 */
  is_pre_signal?: boolean;
  /** 長閉塞信号 */
  is_longblock_signal?: boolean;
  /** 優先信号 */
  is_priority_signal?: boolean;
  /** ルート選択エリア終端 */
  is_end_of_choose?: boolean;
  /** 標識/信号タイプ (文字列) */
  sign_type?: string;
}

/**
 * 建物データ型
 * Building data type
 */
export interface BuildingData extends BaseObj {
  /** 建物タイプID (1=都市観光地, 33=車両基地, 34=停留所, 37=住宅, 38=商業, 39=工業, etc) */
  type?: number;
  /** 建物タイプ名 (例: "Stop", "Depot", "Residential") */
  type_str?: string;
  /** 建物レベル（価格・容量計算に使用） */
  level?: number;
  /** 幅（タイル数） */
  size_x?: number;
  /** 奥行き（タイル数） */
  size_y?: number;
  /** レイアウト数 (1,2,4,8,16,48) */
  layouts?: number;
  /** 許可される気候（ビットマスク, version 4+） */
  allowed_climates?: number;
  /** 許可される気候（文字列, version 4+） */
  allowed_climates_str?: string;
  /** 有効化機能ビット (0x01=乗客, 0x02=郵便, 0x04=貨物) */
  enables?: number;
  /** 有効化機能名 (例: "Passengers, Mail") */
  enables_str?: string;
  /** フラグビット（情報非表示、建設ピット無し等） */
  flags?: number;
  /** 出現確率（重み） */
  distribution_weight?: number;
  /** アニメーション間隔 (ms) */
  animation_time?: number;
  /** 容量（駅の場合は乗客数） */
  capacity?: number;
  /** 維持費 (単位: 0.01cr/月) */
  maintenance?: number;
  /** 建設価格 (単位: 100cr) */
  price?: number;
  /** 地下建設可否 (0=不可, 1=地下のみ, 2=どちらでも) */
  allow_underground?: number;
  /** 保存指定日（リノベーション禁止） */
  preservation_year_month?: number;
  /** 道路タイプID（輸送施設の場合: 1=線路, 3=道路, 4=水路, 16=空路） */
  waytype?: number;
  /** 道路タイプ名 (例: "Track", "Road", "Water") */
  waytype_str?: string;
  /** クラスター番号（都市建物の場合、0=クラスター化なし） */
  cluster?: number;
  /** 最小人口（観光地の場合） */
  min_population?: number;
  /** 本部レベル（プレイヤー本部の場合） */
  hq_level?: number;
}

/**
 * Pakメタデータ型
 * Pak metadata type
 */
export interface PakMetadata extends BaseObj {
  /** オブジェクト名 */
  name: string;
  /** 著作権情報 */
  copyright: string | null;
  /** オブジェクトタイプ (例: "vehicle", "way", "building") */
  objectType: string;
  /** コンパイラバージョンコード */
  compilerVersionCode: number;
  /** 車両データ (objectType="vehicle"の場合のみ) */
  vehicleData?: VehicleData;
  /** 道路データ (objectType="way"の場合のみ) */
  wayData?: WayData;
  /** Way-object データ (objectType="way-object"の場合のみ, 架線等) */
  wayObjectData?: WayObjectData;
  /** 建物データ (objectType="building"の場合のみ) */
  buildingData?: BuildingData;
  /** 橋データ (objectType="bridge"の場合のみ) */
  bridgeData?: BridgeData;
  /** トンネルデータ (objectType="tunnel"の場合のみ) */
  tunnelData?: TunnelData;
  /** 道路標識/信号データ (objectType="roadsign"の場合のみ) */
  signData?: SignData;
  /** 踏切データ (objectType="crossing"の場合のみ) */
  crossingData?: CrossingData;
  /** 市内自動車データ (objectType="citycar"の場合のみ) */
  citycarData?: CitycarData;
  /** 工場データ (objectType="factory"の場合のみ) */
  factoryData?: FactoryData;
  /** 貨物データ (objectType="good"の場合のみ) */
  goodData?: GoodData;
  /** 歩行者データ (objectType="pedestrian"の場合のみ) */
  pedestrianData?: PedestrianData;
  /** 樹木データ (objectType="tree"の場合のみ) */
  treeData?: TreeData;
  /** 地上オブジェクトデータ (objectType="groundobj"の場合のみ) */
  groundobjData?: GroundobjData;
  /** 地形テクスチャデータ (objectType="ground"の場合のみ) */
  groundData?: GroundData;
  /** 効果音データ (objectType="sound"の場合のみ) */
  soundData?: SoundData;
  /** スキン/UI画像データ (objectType="skin" または "smoke"の場合のみ) */
  skinData?: SkinData;
}

/**
 * ファイル情報表示型
 * FileInfo display type
 */
export interface FileInfoShow {
  data: {
    dats: Record<string, string[]>;
    tabs: Record<string, Record<string, string>>;
    paks_metadata?: Record<string, PakMetadata[]>;
  };
}

/**
 * マイページファイル情報編集型
 * Mypage FileInfo edit type
 */
export interface FileInfoMypageEdit {
  data: {
    dats: Record<string, string[]>;
    tabs: Record<string, Record<string, string>>;
    paks: Record<string, string[]>;
    paks_metadata?: Record<string, PakMetadata[]>;
    readmes: Record<string, string[]>;
  };
}
