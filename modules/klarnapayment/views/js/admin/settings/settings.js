/**
 * NOTICE OF LICENSE
 *
 * @author    Klarna Bank AB www.klarna.com
 * @copyright Copyright (c) permanent, Klarna Bank AB
 * @license   ISC
 * @see       /LICENSE
 *
 * International Registered Trademark & Property of Klarna Bank AB
 */

const klarnapaymentCollapsedPanelsStorageKey = 'klarnapayment_collapsed_panels';
const klarnapaymentCollapsedCredentialsStorageKey = 'klarnapayment_collapsed_credentials';

$(document).ready(function () {
  $('[data-toggle="klarnapayment-tooltip"]').tooltip();
  $('.klarna-siwk-required-checkbox').attr('disabled', true);

  $('.environment-switch').click(function (event) {
    event.preventDefault();

    if(!confirm('Do you want to switch environments?')) {
      this.blur();
    } else {
      window.location.href = $(this).val();
    }
  })

  $('.copy-icon-button').click(function (event) {
    const input = $(this).closest('.form-group').find('input');
    navigator.clipboard.writeText(input.val());
  })

  if (klarnapayment.production) {
    $('.environment-switch.production').attr('checked', true);
  } else {
    $('.environment-switch.sandbox').attr('checked', true);
  }

  $('.osm-theme-select').on('change', function() {
    updateAllOsmPreviewImages();
  });

  $('.osm-placement-select').on('change', function() {
    const placement = $(this).attr('name');
    const placementValue = $(this).val();

    updateOsmPreviewImagesForPlacement(placement, placementValue);
  });

  $('.kec-theme-select, .kec-shape-select').on('change', function() {
    updateKecPreviewImage();
  });

  $('.siwk-theme-select, .siwk-shape-select, .siwk-alignment-select').on('change', function() {
    updateAllSiwkPreviewImages();
  });

  collapsePanels();
  collapseCredentials();

  $('#content form .panel-heading > i').on('click', function () {
    const $panelWrapper = $(this).closest('form');

    toggleCollapsePanel($panelWrapper);
  });

  $('.klarna-credential-trigger').on('click', function (event) {
    const closestCollapse = $(this).closest('.region-container').find('.credentials-container');
    toggleCollapseCredentials(closestCollapse, $(this));
  });
});

const updateAllOsmPreviewImages = () => {
  $('.osm-placement-select').each(function () {
    const placementKey = $(this).attr('name');
    const placementValue = $(this).val();

    updateOsmPreviewImagesForPlacement(placementKey, placementValue);
  });
}

const updateOsmPreviewImagesForPlacement = (placement, placementValue) => {
  let selectedTheme = getSelectedOsmTheme();
  let $previewImageElement = $('.osm-img[data-key='+placement+']');

  if (klarnapayment_image_urls?.OSM?.[selectedTheme]?.[placement]?.[placementValue] !== undefined) {
    $previewImageElement.attr('src', klarnapayment_image_urls.OSM[selectedTheme][placement][placementValue]);
    $previewImageElement.closest('.osm-img-wrapper').removeClass('hidden');
  } else {
    $previewImageElement.closest('.osm-img-wrapper').addClass('hidden');
  }
}

const getSelectedOsmTheme = () => {
  const theme = $('.osm-theme-select').val();

  return theme === 'default' ? 'light' : theme;
}

const updateAllSiwkPreviewImages = () => {
  const selectedTheme = $('.siwk-theme-select').val();
  const selectedShape = $('.siwk-shape-select').val();
  const selectedAlignment = $('.siwk-alignment-select').val();
  const previewImageElement = $('.siwk-preview');

  if (klarnapayment_image_urls?.SIWK?.[selectedTheme]?.[selectedShape]?.[selectedAlignment] !== undefined) {
    previewImageElement.attr('src', klarnapayment_image_urls.SIWK[selectedTheme][selectedShape][selectedAlignment]);
    previewImageElement.closest('.siwk-image-wrapper').removeClass('hidden');
  } else {
    previewImageElement.closest('.siwk-image-wrapper').addClass('hidden');
  }
}

const updateKecPreviewImage = () => {
  const selectedTheme = $('.kec-theme-select').val();
  const selectedShape = $('.kec-shape-select').val();
  const $previewImageElement = $('.kec-preview');

  if (klarnapayment_image_urls?.KEC?.[selectedTheme]?.[selectedShape] !== undefined) {
    $previewImageElement.attr('src', klarnapayment_image_urls.KEC[selectedTheme][selectedShape]);
    $previewImageElement.closest('.kec-image-wrapper').removeClass('hidden');
  } else {
    $previewImageElement.closest('.kec-image-wrapper').addClass('hidden');
  }
}

const toggleCollapsePanel = ($panelWrapper) => {
  const $panel = $panelWrapper.find('.panel');
  const $toggleButton = $panel.find('.panel-heading > i').first();
  const $formWrapper = $panel.find('.form-wrapper').first();
  const panelWrapperId = '#' + $panelWrapper.attr('id');

  $toggleButton.toggleClass('icon-chevron-up icon-chevron-down');

  if ($formWrapper.is(':hidden')) {
    $panel.removeClass('klarna-panel-collapsed');
  }

  $panel.find('.form-wrapper, .panel-footer').slideToggle('fast', function () {
    if ($formWrapper.is(':hidden')) {
      $panel.addClass('klarna-panel-collapsed');
      setCollapseState(panelWrapperId, true);
    } else {
      setCollapseState(panelWrapperId, false);
    }
  });
}

const setCollapseState = (panelWrapperSelector, collapsed) => {
  let collapsedPanels = JSON.parse(localStorage.getItem(klarnapaymentCollapsedPanelsStorageKey) ?? '[]');
  collapsedPanels = collapsedPanels.filter(panel => panel !== panelWrapperSelector);

  if (collapsed) {
    collapsedPanels.push(panelWrapperSelector);
  }

  localStorage.setItem(klarnapaymentCollapsedPanelsStorageKey, JSON.stringify(collapsedPanels));
}

const collapsePanels = () => {
  const collapsedPanels = JSON.parse(localStorage.getItem(klarnapaymentCollapsedPanelsStorageKey) ?? '[]');

  collapsedPanels.forEach((panelWrapperSelector) => {
    const $panel = $(panelWrapperSelector).find('.panel');
    $panel.find('.form-wrapper, .panel-footer').hide();
    $panel.addClass('klarna-panel-collapsed');
  });
}

const toggleCollapseCredentials = ($collapseElement, $toggleButton) => {
  let $cacheItem = '#' + $collapseElement.attr('id');

  $toggleButton.find('i').toggleClass('icon-chevron-up icon-chevron-down');

  $collapseElement.slideToggle('fast', function () {
    setCollapseCredentialsState($cacheItem, $collapseElement.is(':hidden'));
  });
}

const setCollapseCredentialsState = (cacheItem, collapsed) => {
  let collapsedCredentials = JSON.parse(localStorage.getItem(klarnapaymentCollapsedCredentialsStorageKey) ?? '[]');
  collapsedCredentials = collapsedCredentials.filter(item => item !== cacheItem);

  if (collapsed) {
    collapsedCredentials.push(cacheItem);
  }

  localStorage.setItem(klarnapaymentCollapsedCredentialsStorageKey, JSON.stringify(collapsedCredentials));
}

const collapseCredentials = () => {
  const collapsedCredentials = JSON.parse(localStorage.getItem(klarnapaymentCollapsedCredentialsStorageKey) ?? '[]');

  collapsedCredentials.forEach((collapsedCredentials) => {
    let $panel = $(collapsedCredentials);
    $panel.hide();
  });
}